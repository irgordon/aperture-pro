<?php

namespace AperturePro\Domain\Jobs;

use AperturePro\Support\Cache;

class JobRepository
{
    private static function getCacheKey(int $id): string
    {
        return "job_{$id}";
    }

    protected static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'ap_jobs';
    }

    private static function insert(Job $job): int
    {
        global $wpdb;

        $wpdb->insert(
            self::table(),
            [
                'project_id'   => $job->project_id,
                'type'         => $job->type,
                'status'       => $job->status,
                'attempts'     => $job->attempts,
                'max_attempts' => $job->max_attempts,
                'last_error'   => $job->last_error,
                'payload'      => $job->payload,
                'created_at'   => $job->created_at,
                'updated_at'   => $job->updated_at,
            ],
            ['%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }

    private static function update(Job $job): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            [
                'project_id'   => $job->project_id,
                'type'         => $job->type,
                'status'       => $job->status,
                'attempts'     => $job->attempts,
                'max_attempts' => $job->max_attempts,
                'last_error'   => $job->last_error,
                'payload'      => $job->payload,
                'created_at'   => $job->created_at,
                'updated_at'   => $job->updated_at,
            ],
            ['id' => $job->id],
            ['%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s'],
            ['%d']
        );
    }

    public static function save(Job $job): void
    {
        if ($job->id > 0) {
            self::update($job);
        } else {
            $job->id = self::insert($job);
        }

        self::clearCache($job);
    }

    public static function create(int $project_id, string $type, array $payload = []): int
    {
        global $wpdb;

        $wpdb->insert(
            self::table(),
            [
                'project_id'   => $project_id,
                'type'         => $type,
                'status'       => JobState::QUEUED,
                'attempts'     => 0,
                'max_attempts' => 3,
                'last_error'   => null,
                'payload'      => wp_json_encode($payload),
                'created_at'   => current_time('mysql'),
                'updated_at'   => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }

    public static function find(int $id): ?Job
    {
        global $wpdb;

        $cacheKey = self::getCacheKey($id);
        $job = Cache::get($cacheKey);

        if ($job instanceof Job) {
            return $job;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::table() . ' WHERE id = %d',
                $id
            )
        );

        if (!$row) {
            return null;
        }

        $job = new Job($row);
        Cache::put($cacheKey, $job);

        return $job;
    }

    public static function clearCache(Job $job): void
    {
        Cache::forget(self::getCacheKey($job->id));
    }

    public static function updateStatus(Job $job, string $status, ?string $error = null): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            [
                'status'     => $status,
                'last_error' => $error,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $job->id],
            ['%s', '%s', '%s'],
            ['%d']
        );
    }

    public static function incrementAttempts(Job $job): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            [
                'attempts'   => $job->attempts + 1,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $job->id],
            ['%d', '%s'],
            ['%d']
        );
    }

    public static function updatePayload(Job $job, array $payload): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            [
                'payload'    => wp_json_encode($payload),
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $job->id],
            ['%s', '%s'],
            ['%d']
        );
    }
}
