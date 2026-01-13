<?php

namespace AperturePro\Http\Middleware;

use WP_Error;
use WP_REST_Request;

class Input
{
    public static function int(WP_REST_Request $request, string $key, ?int $default = null): ?int
    {
        $val = $request->get_param($key);
        if ($val === null || $val === '') {
            return $default;
        }
        return (int) $val;
    }

    public static function bool(WP_REST_Request $request, string $key, bool $default = false): bool
    {
        $val = $request->get_param($key);
        if ($val === null || $val === '') {
            return $default;
        }

        $filtered = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return $filtered ?? $default;
    }

    public static function enum(WP_REST_Request $request, string $key, array $allowed, string $default = '')
    {
        $val = (string) $request->get_param($key);
        if (in_array($val, $allowed, true)) {
            return $val;
        }
        return $default;
    }

    public static function required(WP_REST_Request $request, string $key)
    {
        $val = $request->get_param($key);
        if ($val === null || $val === '') {
            throw new WP_Error(
                'ap_missing_field',
                sprintf('Missing required field: %s', $key),
                ['status' => 400]
            );
        }
        return $val;
    }
}
