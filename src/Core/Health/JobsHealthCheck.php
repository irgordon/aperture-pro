<?php

namespace AperturePro\Core\Health;

class JobsHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Background jobs are running smoothly.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Background Jobs';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDetails()
    {
        return $this->details;
    }

    private function runCheck(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_jobs';

        // Check for table existence first
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
             // Schema check handles this, but we should handle it gracefully here
             $this->status = 'warning';
             $this->message = 'Jobs table missing (checked in Schema).';
             return;
        }

        // Check for failed jobs in the last 24 hours
        $failedCount = $wpdb->get_var("
            SELECT COUNT(*) FROM {$table}
            WHERE status = 'failed'
            AND created_at > NOW() - INTERVAL 1 DAY
        ");

        if ($failedCount > 0) {
            $this->status = 'warning';
            $this->message = "There are {$failedCount} failed jobs in the last 24 hours.";
            $this->details[] = "Failed Jobs (24h): {$failedCount}";
        } else {
            $this->details[] = "Failed Jobs (24h): 0";
        }

        // Check for pending jobs count
        $pendingCount = $wpdb->get_var("
            SELECT COUNT(*) FROM {$table}
            WHERE status = 'pending'
        ");

        if ($pendingCount > 50) {
            $this->status = 'warning'; // Yellow
            $this->message = "High number of pending jobs: {$pendingCount}.";
            $this->details[] = "Pending Jobs: {$pendingCount}";
        } else {
            $this->details[] = "Pending Jobs: {$pendingCount}";
        }
    }
}
