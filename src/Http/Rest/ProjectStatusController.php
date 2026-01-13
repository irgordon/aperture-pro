<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Domain\Project\ProjectStatusService;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Support\Cache;

class ProjectStatusController extends BaseController
{
    public function register_routes(): void
    {
        $this->register_route('aperture-pro/v1', '/projects/(?P<id>\d+)/status', [
            'methods'             => 'GET',
            'callback'            => [$this, 'show'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_view_project'],
        ]);
    }

    public function show(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        // Cache status for 5 minutes
        return Cache::remember(
            Cache::key('status', $project_id),
            function () use ($project_id) {
                return (new ProjectStatusService())->get_status($project_id);
            },
            300
        );
    }
}
