<?php

namespace AperturePro\Support;

class Str
{
    public static function uuid(): string
    {
        return wp_generate_uuid4();
    }

    public static function random(int $length = 32): string
    {
        return wp_generate_password($length, false);
    }

    public static function startsWith(string $haystack, string $needle): bool
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
