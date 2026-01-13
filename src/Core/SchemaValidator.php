<?php

namespace AperturePro\Core;

use AperturePro\Domain\Logs\Logger;

class SchemaValidator
{
    /**
     * Returns an array of human-readable issues.
     */
    public static function validate(): array
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

        // Example index checks (simple, non‑exhaustive)
        $jobsTable = $wpdb->prefix . 'ap_jobs';
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

        if (!empty($issues)) {
            Logger::warning('Schema validation issues detected', ['issues' => $issues]);
        }

        return $issues;
    }
}
