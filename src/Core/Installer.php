<?php

namespace AperturePro\Core;

use AperturePro\Domain\Logs\Logger;

class Installer
{
    // Bump this when you add new migrations
    public const VERSION = '1.0.0';

    public static function activate(): void
    {
        self::runMigrations();
        update_option('ap_db_version', self::VERSION);
    }

    public static function deactivate(): void
    {
        // Unschedule jobs, timers, etc.
        wp_clear_scheduled_hook('ap_run_job');
    }

    /**
     * Run all pending migrations in order.
     * Idempotent: only runs migrations newer than stored version.
     */
    protected static function runMigrations(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $installed = get_option('ap_db_version', '0.0.0');

        $migrationsDir = __DIR__ . '/../../sql/migrations';
        if (!is_dir($migrationsDir)) {
            return;
        }

        $files = glob($migrationsDir . '/*.sql') ?: [];
        usort($files, static function ($a, $b) {
            return strcmp(basename($a), basename($b));
        });

        foreach ($files as $file) {
            // Convention: 001_create_core_tables.sql → "001"
            $basename = basename($file);
            $versionKey = substr($basename, 0, 3);

            // Only run if not yet applied
            if (version_compare($installed, $versionKey, '>=')) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                Logger::error('Failed to read migration file', ['file' => $file]);
                continue;
            }

            try {
                dbDelta($sql);
                Logger::info('Migration applied', ['file' => $file, 'version' => $versionKey]);
            } catch (\Throwable $e) {
                Logger::error('Migration failed', [
                    'file'   => $file,
                    'error'  => $e->getMessage(),
                ]);
                // Do not silently swallow; log and stop further migrations
                break;
            }
        }
    }
}
