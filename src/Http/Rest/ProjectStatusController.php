<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Domain\Project\ProjectStatusService;
use AperturePro\Http\Middleware\Permissions;

class ProjectStatusController
{
    public function register_routes(): void
    {
        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/status', [
            'methods'             => 'GET',
            'callback'            => [$this, 'show'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_view_project'],
        ]);
    }

    public function show(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];
        $service    = new ProjectStatusService();

        return $service->get_status($project_id);
    }
}
