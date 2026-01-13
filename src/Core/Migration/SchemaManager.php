<?php

namespace AperturePro\Core\Migration;

use AperturePro\Domain\Logs\Logger;

class SchemaManager
{
    private const OPTION_KEY = 'ap_applied_migrations';

    public function migrate(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $applied = get_option(self::OPTION_KEY, []);
        $migrationsDir = dirname(__DIR__, 3) . '/sql/migrations';

        if (!is_dir($migrationsDir)) {
            Logger::error('Migration directory not found', ['dir' => $migrationsDir]);
            return;
        }

        $files = glob($migrationsDir . '/*.sql') ?: [];
        sort($files); // Ensure order

        $updated = false;

        foreach ($files as $file) {
            $basename = basename($file);

            if (in_array($basename, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            if (!$sql) {
                Logger::error('Failed to read migration file', ['file' => $file]);
                continue;
            }

            // Replace {prefix} with actual prefix
            $sql = str_replace('{prefix}', $wpdb->prefix, $sql);

            try {
                // dbDelta handles multiple queries in one string if they are separated by ;?
                // dbDelta splits by ; internally.
                dbDelta($sql);

                $applied[] = $basename;
                update_option(self::OPTION_KEY, $applied);

                Logger::info('Migration applied', ['file' => $basename]);
                $updated = true;
            } catch (\Throwable $e) {
                Logger::error('Migration failed', [
                    'file' => $basename,
                    'error' => $e->getMessage(),
                ]);
                throw $e; // Stop migration on failure
            }
        }
    }

    /**
     * Check if there are pending migrations.
     */
    public function hasPending(): bool
    {
        $applied = get_option(self::OPTION_KEY, []);
        $migrationsDir = dirname(__DIR__, 3) . '/sql/migrations';

        if (!is_dir($migrationsDir)) {
            return false;
        }

        $files = glob($migrationsDir . '/*.sql') ?: [];
        foreach ($files as $file) {
            if (!in_array(basename($file), $applied, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get list of applied migrations.
     */
    public function getApplied(): array
    {
        return get_option(self::OPTION_KEY, []);
    }
}
