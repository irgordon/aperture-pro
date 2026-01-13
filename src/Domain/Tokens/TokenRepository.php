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

        $cached = wp_cache_get($token, 'ap_tokens');
        if (false !== $cached) {
            return $cached;
        }

        $row = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM ' . self::table() . ' WHERE token = %s LIMIT 1',
                $token
            )
        );

        if ($row) {
            wp_cache_set($token, $row, 'ap_tokens');
        }

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

        wp_cache_delete($token, 'ap_tokens');
    }

    public static function revokeProjectTokens(int $project_id, string $type): void
    {
        global $wpdb;

        // Fetch tokens to be revoked to clear cache
        $tokens = $wpdb->get_col(
            $wpdb->prepare(
                'SELECT token FROM ' . self::table() . ' WHERE project_id = %d AND type = %s AND used = 0',
                $project_id,
                $type
            )
        );

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

        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                wp_cache_delete($token, 'ap_tokens');
            }
        }
    }
}
