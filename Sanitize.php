<?php

namespace AperturePro\Support;

class Sanitize
{
    public static function int($value, ?int $default = null): ?int
    {
        if ($value === null || $value === '') {
            return $default;
        }
        return (int) $value;
    }

    public static function bool($value, bool $default = false): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $filtered ?? $default;
    }

    public static function text($value): string
    {
        return sanitize_text_field($value);
    }

    public static function textarea($value): string
    {
        return sanitize_textarea_field($value);
    }

    public static function html($value): string
    {
        return wp_kses_post($value);
    }

    public static function arrayOfInts($values): array
    {
        if (!is_array($values)) {
            return [];
        }

        return array_map('intval', $values);
    }
}
