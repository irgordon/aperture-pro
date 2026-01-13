<?php

namespace AperturePro\Support;

use WP_Error;
use AperturePro\Domain\Logs\Logger;

class Error
{
    public static function throw(string $message, string $code = 'ap_error', int $status = 400): void
    {
        throw new WP_Error($code, $message, ['status' => $status]);
    }

    public static function logAndThrow(string $message, array $context = [], string $code = 'ap_error', int $status = 400): void
    {
        Logger::error($message, $context);
        throw new WP_Error($code, $message, ['status' => $status]);
    }

    public static function fromException(\Throwable $e, int $status = 500): WP_Error
    {
        Logger::error('Exception caught', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return new WP_Error('ap_exception', $e->getMessage(), ['status' => $status]);
    }
}
