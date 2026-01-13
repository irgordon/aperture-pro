<?php

namespace AperturePro\Support;

use WP_Error;

class Response
{
    public static function success(array $data = [], int $status = 200): array
    {
        return array_merge(['success' => true], $data, ['status' => $status]);
    }

    public static function error(string $message, string $code = 'ap_error', int $status = 400): WP_Error
    {
        return new WP_Error($code, $message, ['status' => $status]);
    }

    public static function forbidden(string $message = 'Forbidden'): WP_Error
    {
        return self::error($message, 'ap_forbidden', 403);
    }

    public static function notFound(string $message = 'Not found'): WP_Error
    {
        return self::error($message, 'ap_not_found', 404);
    }

    public static function validation(string $message): WP_Error
    {
        return self::error($message, 'ap_validation_error', 422);
    }
}
