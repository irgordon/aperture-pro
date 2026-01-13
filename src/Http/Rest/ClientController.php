<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use WP_Error;
use AperturePro\Http\Middleware\Permissions;

class ClientController extends BaseController
{
    public function register_routes(): void
    {
        $this->register_route('aperture-pro/v1', '/projects/(?P<id>\d+)/client', [
            'methods'             => 'POST',
            'callback'            => [$this, 'update'],
            'permission_callback' => [Permissions::class, 'admin_can_manage_project'],
        ]);
    }

    public function update(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];
        $name       = sanitize_text_field($req['name']);
        $email      = sanitize_email($req['email']);

        if (!$name || !$email) {
            return new WP_Error('invalid_data', 'Name and email are required.', ['status' => 400]);
        }

        update_post_meta($project_id, 'ap_client_name', $name);
        update_post_meta($project_id, 'ap_client_email', $email);

        return [
            'success' => true,
            'client'  => [
                'name'  => $name,
                'email' => $email,
            ],
        ];
    }
}
