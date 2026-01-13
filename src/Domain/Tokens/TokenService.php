<?php

namespace AperturePro\Domain\Tokens;

use AperturePro\Domain\Logs\Logger;

class TokenService
{
    public static function generateAccessToken(int $project_id): string
    {
        // Only one active access token per project
        TokenRepository::revokeProjectTokens($project_id, TokenTypes::ACCESS);

        $token   = wp_generate_password(64, false);
        $expires = gmdate('Y-m-d H:i:s', time() + WEEK_IN_SECONDS);

        TokenRepository::create($project_id, $token, TokenTypes::ACCESS, $expires);

        Logger::info('Access token generated', ['token' => $token], $project_id);

        return $token;
    }

    public static function generateDownloadToken(int $project_id): string
    {
        // Download tokens are single-use; do not revoke previous ones
        $token   = wp_generate_password(64, false);
        $expires = gmdate('Y-m-d H:i:s', time() + WEEK_IN_SECONDS);

        TokenRepository::create($project_id, $token, TokenTypes::DOWNLOAD, $expires);

        Logger::info('Download token generated', ['token' => $token], $project_id);

        return $token;
    }

    public static function validate(int $project_id, string $token): bool
    {
        $record = TokenRepository::find($token);

        if (!$record || (int) $record->project_id !== $project_id) {
            return false;
        }

        return TokenValidator::validateTokenObject($record);
    }

    public static function validateProjectAccessToken(int $project_id): bool
    {
        $token = self::extractToken();
        if (!$token) {
            return false;
        }

        return self::validate($project_id, $token);
    }

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

    public static function markUsed(string $token): void
    {
        TokenRepository::markUsed($token);
    }
}
