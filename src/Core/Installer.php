<?php

namespace AperturePro\Core;

class Installer
{
    public static function activate(): void
    {
        self::create_tables();
        update_option('ap_db_version', '1.0.0');
    }

    public static function deactivate(): void
    {
        // unschedule jobs, etc.
    }

    protected static function create_tables(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $jobs    = $wpdb->prefix . 'ap_jobs';
        $tokens  = $wpdb->prefix . 'ap_tokens';
        $logs    = $wpdb->prefix . 'ap_logs';

        $sql = "
        CREATE TABLE $jobs (
          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          project_id BIGINT UNSIGNED NOT NULL,
          type VARCHAR(50) NOT NULL,
          status VARCHAR(20) NOT NULL,
          attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
          max_attempts TINYINT UNSIGNED NOT NULL DEFAULT 3,
          last_error TEXT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          KEY project_status (project_id, status),
          KEY created_at (created_at)
        ) $charset;

        CREATE TABLE $tokens (
          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          project_id BIGINT UNSIGNED NOT NULL,
          token CHAR(64) NOT NULL,
          type VARCHAR(20) NOT NULL,
          used TINYINT(1) NOT NULL DEFAULT 0,
          expires_at DATETIME NOT NULL,
          created_at DATETIME NOT NULL,
          UNIQUE KEY token (token),
          KEY project_type (project_id, type)
        ) $charset;

        CREATE TABLE $logs (
          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          project_id BIGINT UNSIGNED NULL,
          level VARCHAR(20) NOT NULL,
          message TEXT NOT NULL,
          context LONGTEXT NULL,
          created_at DATETIME NOT NULL,
          KEY project_level (project_id, level),
          KEY created_at (created_at)
        ) $charset;
        ";

        dbDelta($sql);
    }
}
