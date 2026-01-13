<?php

namespace AperturePro\Core\Health;

use AperturePro\Domain\Logs\Logger;

class SchemaHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Database schema is correct.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Database Schema';
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

        $issues = [];
        $requiredTables = [
            'ap_jobs',
            'ap_tokens',
            'ap_logs',
        ];

        foreach ($requiredTables as $table) {
            $name = $wpdb->prefix . $table;
            $exists = $wpdb->get_var($wpdb->prepare(
                "SHOW TABLES LIKE %s",
                $name
            ));

            if ($exists !== $name) {
                $issues[] = "Missing table: {$name}";
            }
        }

        // Example index checks
        $jobsTable = $wpdb->prefix . 'ap_jobs';
        // Check if table exists before checking index to avoid SQL error
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $jobsTable)) === $jobsTable) {
            $indexes = $wpdb->get_results("SHOW INDEX FROM {$jobsTable}", ARRAY_A);
            $hasProjectStatusIndex = false;
            foreach ($indexes as $idx) {
                if ($idx['Key_name'] === 'project_status') {
                    $hasProjectStatusIndex = true;
                    break;
                }
            }

            if (!$hasProjectStatusIndex) {
                $issues[] = "Missing index 'project_status' on {$jobsTable}";
            }
        }

        if (!empty($issues)) {
            $this->status = 'error';
            $this->message = 'Database schema issues detected. Some features may not work.';
            $this->details = $issues;

            // Log warning but don't email here (Logger logic handles email on error, not warning)
            Logger::warning('Schema validation issues detected', ['issues' => $issues]);
        }
    }
}
