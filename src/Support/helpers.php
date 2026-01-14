<?php

use AperturePro\Domain\Logs\Logger;
use AperturePro\Domain\Notification\NotificationService;

if (!function_exists('ap_get_project_images')) {
    /**
     * Get images attached to a project, with pagination support.
     *
     * @param int $project_id
     * @param int $limit
     * @param int $offset
     * @return WP_Post[]
     */
    function ap_get_project_images(int $project_id, int $limit = -1, int $offset = 0, string $fields = ''): array
    {
        $args = [
            'post_type'      => 'attachment',
            'post_parent'    => $project_id,
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'post_mime_type' => 'image',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        if (!empty($fields)) {
            $args['fields'] = $fields;
        }

        return get_posts($args);
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
        // Use NotificationService for email sending
        // This acts as a facade or bridge to the service
        $service = new NotificationService();

        $email = null;
        if ($recipient === 'client') {
            $email = $service->getClientEmail($project_id);
        } elseif ($recipient === 'admin') {
            $email = get_option('admin_email');
        }

        if ($email) {
             // Derive a basic subject from the type
             $subject = "Notification: " . str_replace('_', ' ', ucfirst($type));
             $service->sendEmail($email, $subject, $message);
        } else {
             Logger::info("Notification skipped for {$recipient} (no email found)", [
                'type'       => $type,
                'message'    => $message,
                'project_id' => $project_id,
            ], $project_id);
        }
    }
}
