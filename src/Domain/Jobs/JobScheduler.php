<?php

namespace AperturePro\Domain\Jobs;

use AperturePro\Domain\Logs\Logger;

class JobScheduler
{
    public static function enqueue(int $project_id, string $type, array $payload = []): int
    {
        $job_id = JobRepository::create($project_id, $type, $payload);

        wp_schedule_single_event(time(), 'ap_run_job', [$job_id]);

        Logger::info('Job queued', [
            'job_id'     => $job_id,
            'type'       => $type,
            'project_id' => $project_id,
        ], $project_id);

        return $job_id;
    }

    public static function scheduleRetry(Job $job): void
    {
        $attempt = $job->attempts + 1;

        if ($attempt > $job->max_attempts) {
            JobRepository::updateStatus($job, JobState::DEAD_LETTER, $job->last_error);

            Logger::error('Job moved to dead-letter queue', [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'error'      => $job->last_error,
            ], $job->project_id);

            do_action(JobEvents::DEAD_LETTER, [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'type'       => $job->type,
            ]);

            return;
        }

        $delay = match ($attempt) {
            1 => 0,
            2 => 30,
            3 => 120,
            default => 0,
        };

        JobRepository::incrementAttempts($job);
        JobRepository::updateStatus($job, JobState::RETRYING, $job->last_error);

        wp_schedule_single_event(time() + $delay, 'ap_run_job', [$job->id]);

        Logger::warning('Job scheduled for retry', [
            'job_id'     => $job->id,
            'project_id' => $job->project_id,
            'attempt'    => $attempt,
            'delay'      => $delay,
        ], $job->project_id);

        do_action(JobEvents::RETRYING, [
            'job_id'     => $job->id,
            'project_id' => $job->project_id,
            'type'       => $job->type,
            'attempt'    => $attempt,
            'delay'      => $delay,
        ]);
    }
}
