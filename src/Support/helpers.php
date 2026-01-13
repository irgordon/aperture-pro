<?php

use AperturePro\Domain\Logs\Logger;

if (!function_exists('ap_get_project_images')) {
    /**
     * Get all images attached to a project.
     *
     * @param int $project_id
     * @return WP_Post[]
     */
    function ap_get_project_images(int $project_id): array
    {
        return get_posts([
            'post_type'      => 'attachment',
            'post_parent'    => $project_id,
            'posts_per_page' => -1,
            'post_mime_type' => 'image',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);
    }
}

if (!function_exists('ap_notify')) {
    /**
     * Send a notification.
     *
     * @param int $project_id
     * @param string $recipient 'client' or 'admin'
     * @param string $type
     * @param string $message
     * @return void
     */
    function ap_notify(int $project_id, string $recipient, string $type, string $message): void
    {
        // Mock notification for now
        Logger::info("Notification sent to {$recipient}", [
            'type'       => $type,
            'message'    => $message,
            'project_id' => $project_id,
        ], $project_id);
    }
}
