<?php

namespace AperturePro\Domain\Tokens;

class TokenRepository
{
    protected static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'ap_tokens';
    }

    public static function create(int $project_id, string $token, string $type, string $expires_at): int
    {
        global $wpdb;

        $wpdb->insert(
            self::table(),
            [
                'project_id' => $project_id,
                'token'      => $token,
                'type'       => $type,
                'used'       => 0,
                'expires_at' => $expires_at,
                'created_at' => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%d', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }

    public static function find(string $token): ?object
    {
        global $wpdb;

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::table() . ' WHERE token = %s LIMIT 1',
                $token
            )
        );

        return $row ?: null;
    }

    public static function markUsed(string $token): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            ['used' => 1],
            ['token' => $token],
            ['%d'],
            ['%s']
        );
    }

    public static function revokeProjectTokens(int $project_id, string $type): void
    {
        global $wpdb;

        $wpdb->update(
            self::table(),
            ['used' => 1],
            [
                'project_id' => $project_id,
                'type'       => $type,
            ],
            ['%d'],
            ['%d', '%s']
        );
    }
}
