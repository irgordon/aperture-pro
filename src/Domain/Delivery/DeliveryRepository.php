<?php

namespace AperturePro\Domain\Delivery;

use AperturePro\Domain\Logs\Logger;
use AperturePro\Support\Cache;

class DeliveryRepository
{
    protected static function table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'ap_delivery';
    }

    public static function get(int $project_id): ?object
    {
        global $wpdb;
        $table = self::table();
        $key = Cache::key('delivery', $project_id);

        return Cache::remember($key, function() use ($wpdb, $table, $project_id) {
            return $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table WHERE project_id = %d", $project_id)
            );
        });
    }

    public static function save(int $project_id, string $path, int $size, string $status = 'ready'): bool
    {
        global $wpdb;
        $table = self::table();

        $data = [
            'project_id' => $project_id,
            'zip_path'   => $path,
            'zip_size'   => $size,
            'status'     => $status,
            'updated_at' => current_time('mysql'),
        ];

        // Check if exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE project_id = %d", $project_id));

        $result = false;
        if ($exists) {
            $check = $wpdb->update($table, $data, ['project_id' => $project_id]);
            $result = (bool) $check;
        } else {
            $data['created_at'] = current_time('mysql');
            $check = $wpdb->insert($table, $data);
            $result = (bool) $check;
        }

        if ($result) {
            Cache::forget(Cache::key('delivery', $project_id));
        }

        return $result;
    }
}
