<?php

namespace AperturePro\Domain\Tokens;

use WP_Error;

class TokenValidator
{
    public static function validateTokenObject(?object $token): bool
    {
        if (!$token) {
            return false;
        }

        if ((int) $token->used === 1) {
            return false;
        }

        if (strtotime($token->expires_at) < time()) {
            return false;
        }

        return true;
    }

    public static function assertValid(?object $token): void
    {
        if (!$token) {
            throw new WP_Error(
                'ap_invalid_token',
                'Invalid token.',
                ['status' => 403]
            );
        }

        if ((int) $token->used === 1) {
            throw new WP_Error(
                'ap_token_used',
                'This link has already been used.',
                ['status' => 403]
            );
        }

        if (strtotime($token->expires_at) < time()) {
            throw new WP_Error(
                'ap_token_expired',
                'This link has expired.',
                ['status' => 403]
            );
        }
    }
}
