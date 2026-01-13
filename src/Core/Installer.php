<?php

namespace AperturePro\Core;

use AperturePro\Domain\Logs\Logger;

class Installer
{
    // Bump this when you add new migrations
    public const VERSION = '1.1.0';

    public static function activate(): void
    {
        self::runMigrations();
        self::migrateData();
        update_option('ap_db_version', self::VERSION);

        // Trigger wizard on first install
        if (!get_option('ap_wizard_completed')) {
            set_transient('ap_wizard_pending', true);
        }
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

    protected static function migrateData(): void
    {
        global $wpdb;
        $proofingTable = $wpdb->prefix . 'ap_proofing';

        // Check if proofing table is empty to avoid double migration
        if ($wpdb->get_var("SELECT COUNT(*) FROM $proofingTable") > 0) {
            return;
        }

        // Single query migration:
        // Join postmeta (status) with posts (parent_id) and optionally postmeta (note)
        // This is much faster than iterating.

        $sql = "
            INSERT INTO $proofingTable (project_id, image_id, status, note, updated_at)
            SELECT
                p.post_parent as project_id,
                pm_status.post_id as image_id,
                pm_status.meta_value as status,
                pm_note.meta_value as note,
                NOW() as updated_at
            FROM {$wpdb->postmeta} pm_status
            INNER JOIN {$wpdb->posts} p ON p.ID = pm_status.post_id
            LEFT JOIN {$wpdb->postmeta} pm_note ON pm_note.post_id = pm_status.post_id AND pm_note.meta_key = 'ap_proof_note'
            WHERE pm_status.meta_key = 'ap_proof_status'
            AND p.post_type = 'attachment'
        ";

        // Filter out orphans if needed, but INNER JOIN on posts handles most.
        // We assume images are attachments.

        try {
            $wpdb->query($sql);
            Logger::info('Proofing data migrated to custom table');
        } catch (\Throwable $e) {
            Logger::error('Proofing data migration failed', ['error' => $e->getMessage()]);
        }
    }
}
