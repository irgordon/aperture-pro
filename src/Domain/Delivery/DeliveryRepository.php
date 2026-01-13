<?php

namespace AperturePro\Domain\Delivery;

use AperturePro\Domain\Logs\Logger;

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

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE project_id = %d", $project_id)
        );
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

        if ($exists) {
            return (bool) $wpdb->update($table, $data, ['project_id' => $project_id]);
        } else {
            $data['created_at'] = current_time('mysql');
            return (bool) $wpdb->insert($table, $data);
        }
    }
}
