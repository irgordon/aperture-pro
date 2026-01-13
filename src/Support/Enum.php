<?php

namespace AperturePro\Support;

use WP_Error;

class Enum
{
    public static function validate(string $value, array $allowed, string $errorCode = 'ap_invalid_enum'): string
    {
        if (!in_array($value, $allowed, true)) {
            throw new WP_Error(
                $errorCode,
                sprintf('Invalid value "%s". Allowed: %s', $value, implode(', ', $allowed)),
                ['status' => 400]
            );
        }

        return $value;
    }

    public static function optional(string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
