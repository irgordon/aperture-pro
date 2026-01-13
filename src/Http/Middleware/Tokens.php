<?php

namespace AperturePro\Http\Middleware;

use AperturePro\Domain\Tokens\TokenService;

class Tokens
{
    public static function extractToken(): ?string
    {
        if (!empty($_GET['token'])) {
            return sanitize_text_field(wp_unslash($_GET['token']));
        }

        if (!empty($_SERVER['HTTP_X_APERTURE_TOKEN'])) {
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_APERTURE_TOKEN']));
        }

        return null;
    }

    public static function validateProjectToken(int $project_id): bool
    {
        $token = self::extractToken();
        if (!$token) {
            return false;
        }

        return TokenService::validate($project_id, $token);
    }
}
