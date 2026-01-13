<?php

namespace AperturePro\Domain\Jobs;

class JobRepository
{
    protected static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'ap_jobs';
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

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::table() . ' WHERE id = %d',
                $id
            )
        );

        return $row ? new Job($row) : null;
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
}
