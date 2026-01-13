
import datetime
import json
import re

class MockCache:
    def __init__(self):
        self._cache = {}

    def get(self, key):
        return self._cache.get(key)

    def put(self, key, value, ttl=3600):
        self._cache[key] = value

    def forget(self, key):
        if key in self._cache:
            del self._cache[key]

class MockWpdb:
    def __init__(self):
        self.prefix = 'wp_'
        self.insert_id = 0
        self._data = {self._table_name('ap_jobs'): {}}

    def _table_name(self, table):
        return f"{self.prefix}{table}"

    def insert(self, table, data, format=None):
        table_name = table
        if not table_name.startswith(self.prefix):
            table_name = self._table_name(table)

        self.insert_id += 1
        data['id'] = self.insert_id
        self._data[table_name][self.insert_id] = data
        return self.insert_id

    def update(self, table, data, where, format=None, where_format=None):
        table_name = table
        if not table_name.startswith(self.prefix):
            table_name = self._table_name(table)

        for job_id, job in self._data[table_name].items():
            if job['id'] == where['id']:
                job.update(data)

    def get_row(self, query):
        match = re.search(r"id = (\d+)", query)
        if match:
            job_id = int(match.group(1))
            table_name = self._table_name('ap_jobs')
            return self._data[table_name].get(job_id)
        return None

    def prepare(self, query, *args):
        for arg in args:
            query = query.replace('%d', str(arg), 1)
        return query

wpdb = MockWpdb()

class JobState:
    QUEUED = 'queued'
    RUNNING = 'running'

class Job:
    def __init__(self, row):
        self.id = row['id']
        self.project_id = row['project_id']
        self.type = row['type']
        self.status = row['status']
        self.attempts = row['attempts']
        self.max_attempts = row['max_attempts']
        self.last_error = row['last_error']
        self.payload = row['payload']
        self.created_at = row['created_at']
        self.updated_at = row['updated_at']

class Cache:
    _cache = MockCache()

    @staticmethod
    def get(key):
        return Cache._cache.get(key)

    @staticmethod
    def put(key, value, ttl=3600):
        Cache._cache.put(key, value, ttl)

    @staticmethod
    def forget(key):
        Cache._cache.forget(key)

class JobRepository:
    @staticmethod
    def _get_cache_key(job_id):
        return f"job_{job_id}"

    @staticmethod
    def _table():
        global wpdb
        return f"{wpdb.prefix}ap_jobs"

    @staticmethod
    def create(project_id, type, payload={}):
        global wpdb
        data = {
            'project_id': project_id,
            'type': type,
            'status': JobState.QUEUED,
            'attempts': 0,
            'max_attempts': 3,
            'last_error': None,
            'payload': json.dumps(payload),
            'created_at': datetime.datetime.now().isoformat(),
            'updated_at': datetime.datetime.now().isoformat(),
        }
        job_id = wpdb.insert(JobRepository._table(), data)
        return job_id

    @staticmethod
    def find(job_id):
        global wpdb
        cache_key = JobRepository._get_cache_key(job_id)
        job = Cache.get(cache_key)

        if isinstance(job, Job):
            return job

        row = wpdb.get_row(wpdb.prepare(f"SELECT * FROM {JobRepository._table()} WHERE id = %d", job_id))

        if not row:
            return None

        job = Job(row)
        Cache.put(cache_key, job)
        return job

    @staticmethod
    def clear_cache(job):
        Cache.forget(JobRepository._get_cache_key(job.id))

    @staticmethod
    def update_status(job, status, error=None):
        global wpdb
        wpdb.update(
            JobRepository._table(),
            {'status': status, 'last_error': error, 'updated_at': datetime.datetime.now().isoformat()},
            {'id': job.id}
        )
        JobRepository.clear_cache(job)

def test_job_repository_caching():
    # Create a job
    job_id = JobRepository.create(1, 'test_job')
    assert job_id > 0
    print("PASS: Job created.")

    # Find the job to cache it
    job = JobRepository.find(job_id)
    assert isinstance(job, Job)
    print("PASS: Job found.")

    # Check that the job is cached
    cache_key = JobRepository._get_cache_key(job_id)
    cached_job = Cache.get(cache_key)
    assert isinstance(cached_job, Job)
    assert cached_job.id == job_id
    print("PASS: Job is cached.")

    # Update the job and check that the cache is cleared
    JobRepository.update_status(job, JobState.RUNNING)
    cached_job_after_update = Cache.get(cache_key)
    assert cached_job_after_update is None
    print("PASS: Cache cleared after update.")

    print("\nALL CHECKS PASSED")

if __name__ == "__main__":
    test_job_repository_caching()
