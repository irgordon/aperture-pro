<?php

namespace AperturePro\Support;

class Arr
{
    public static function get(array $array, string $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    public static function mapToInt(array $array): array
    {
        return array_map('intval', $array);
    }
}
