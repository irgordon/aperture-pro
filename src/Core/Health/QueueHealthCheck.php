<?php

namespace AperturePro\Core\Health;

class QueueHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Job queue is healthy.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Job Queue';
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

        // Safety check
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            $this->status = 'error';
            $this->message = 'Jobs table missing.';
            return;
        }

        $stats = $wpdb->get_results("SELECT status, COUNT(*) as count FROM {$table} GROUP BY status", ARRAY_A);
        $counts = [];
        foreach ($stats as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }

        $this->details = $counts;

        $failed = $counts['failed'] ?? 0;
        $pending = $counts['pending'] ?? 0;

        if ($failed > 50) {
            $this->status = 'error';
            $this->message = "High number of failed jobs ({$failed}).";
        } elseif ($failed > 0) {
            $this->status = 'warning';
            $this->message = "There are {$failed} failed jobs.";
        }

        if ($pending > 100) {
            $currentStatus = $this->status;
            // Elevate status if not already error
            if ($currentStatus !== 'error') {
                $this->status = 'warning';
                $this->message = "Large backlog of pending jobs ({$pending}).";
            }
        }
    }
}
