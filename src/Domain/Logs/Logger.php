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
    public static function error(string $m, array $c = [], ?int $p = null): void   { self::log('error', $m, $c, $p); }
}
