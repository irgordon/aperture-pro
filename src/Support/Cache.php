<?php

namespace AperturePro\Support;

class Cache
{
    /**
     * Retrieve an item from the cache.
     *
     * @param string $key
     * @param callable|null $callback If provided, and cache is missing, this will be called to generate value.
     * @param int $ttl Time to live in seconds (default 3600 = 1 hour).
     * @return mixed
     */
    public static function remember(string $key, ?callable $callback = null, int $ttl = 3600)
    {
        $value = get_transient($key);

        if ($value !== false) {
            return $value;
        }

        if ($callback) {
            $value = $callback();
            set_transient($key, $value, $ttl);
            return $value;
        }

        return null;
    }

    /**
     * Get an item from cache.
     */
    public static function get(string $key)
    {
        return get_transient($key);
    }

    /**
     * Store an item in cache.
     */
    public static function put(string $key, $value, int $ttl = 3600): bool
    {
        return set_transient($key, $value, $ttl);
    }

    /**
     * Remove an item from cache.
     */
    public static function forget(string $key): bool
    {
        return delete_transient($key);
    }

    /**
     * Generate a cache key for a project resource.
     */
    public static function key(string $type, int $project_id, string $suffix = ''): string
    {
        return 'ap_' . $type . '_' . $project_id . ($suffix ? '_' . $suffix : '');
    }
}
