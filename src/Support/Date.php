<?php

namespace AperturePro\Support;

class Date
{
    public static function now(): string
    {
        return current_time('mysql');
    }

    public static function utcNow(): string
    {
        return gmdate('Y-m-d H:i:s');
    }

    public static function addSeconds(int $seconds): string
    {
        return gmdate('Y-m-d H:i:s', time() + $seconds);
    }

    public static function addDays(int $days): string
    {
        return gmdate('Y-m-d H:i:s', time() + ($days * DAY_IN_SECONDS));
    }

    public static function isExpired(string $datetime): bool
    {
        return strtotime($datetime) < time();
    }

    public static function diffHuman(string $datetime): string
    {
        $ts = strtotime($datetime);
        $diff = time() - $ts;

        if ($diff < 60) return 'just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';

        return floor($diff / 86400) . ' days ago';
    }
}
