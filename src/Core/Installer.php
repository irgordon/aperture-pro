<?php

namespace AperturePro\Core;

use AperturePro\Domain\Logs\Logger;
use AperturePro\Core\Migration\SchemaManager;

class Installer
{
    // Bump this when you add new migrations
    public const VERSION = '1.1.0';

    public static function activate(): void
    {
        (new SchemaManager())->migrate();
        self::migrateData();
        self::migrateDeliveryData();
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

    protected static function migrateData(): void
    {
        global $wpdb;
        $proofingTable = $wpdb->prefix . 'ap_proofing';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $proofingTable)) !== $proofingTable) {
            return;
        }

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

    protected static function migrateDeliveryData(): void
    {
        global $wpdb;
        $deliveryTable = $wpdb->prefix . 'ap_delivery';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $deliveryTable)) !== $deliveryTable) {
            return;
        }

        // Check if delivery table is empty
        if ($wpdb->get_var("SELECT COUNT(*) FROM $deliveryTable") > 0) {
            return;
        }

        // Migrate postmeta to ap_delivery
        // We need zip_url, zip_size, zip_date
        $sql = "
            INSERT INTO $deliveryTable (project_id, zip_path, zip_size, status, created_at, updated_at)
            SELECT
                pm_url.post_id as project_id,
                pm_url.meta_value as zip_path,
                COALESCE(pm_size.meta_value, 0) as zip_size,
                'ready' as status,
                COALESCE(pm_date.meta_value, NOW()) as created_at,
                COALESCE(pm_date.meta_value, NOW()) as updated_at
            FROM {$wpdb->postmeta} pm_url
            LEFT JOIN {$wpdb->postmeta} pm_size ON pm_size.post_id = pm_url.post_id AND pm_size.meta_key = 'ap_delivery_zip_size'
            LEFT JOIN {$wpdb->postmeta} pm_date ON pm_date.post_id = pm_url.post_id AND pm_date.meta_key = 'ap_delivery_zip_date'
            WHERE pm_url.meta_key = 'ap_delivery_zip_url'
        ";

        try {
            $wpdb->query($sql);
            Logger::info('Delivery data migrated to custom table');
        } catch (\Throwable $e) {
            Logger::error('Delivery data migration failed', ['error' => $e->getMessage()]);
        }
    }
}
