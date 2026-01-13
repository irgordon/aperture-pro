<?php

namespace AperturePro\Domain\Logs;

class Logger
{
    public static function log(string $level, string $message, array $context = [], ?int $project_id = null): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_logs';

        $wpdb->insert($table, [
            'project_id' => $project_id,
            'level'      => $level,
            'message'    => $message,
            'context'    => wp_json_encode($context),
            'created_at' => current_time('mysql'),
        ]);
    }

    public static function info(string $m, array $c = [], ?int $p = null): void    { self::log('info', $m, $c, $p); }
    public static function warning(string $m, array $c = [], ?int $p = null): void { self::log('warning', $m, $c, $p); }
    public static function error(string $m, array $c = [], ?int $p = null): void
    {
        self::log('error', $m, $c, $p);
        self::notifyAdmin($m, $c);
    }

    private static function notifyAdmin(string $message, array $context = []): void
    {
        static $isEmailing = false;

        if ($isEmailing) {
            return;
        }

        $isEmailing = true;
        $to = get_option('admin_email');
        $subject = 'Aperture Pro: Critical Error Detected';
        $body = "A critical error occurred in Aperture Pro:\n\n";
        $body .= "Message: " . $message . "\n";
        $body .= "Context: " . print_r($context, true) . "\n\n";
        $body .= "Please check your site's health status.";

        wp_mail($to, $subject, $body);
        $isEmailing = false;
    }
}
