<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use WP_Error;
use AperturePro\Domain\Jobs\JobScheduler;
use AperturePro\Domain\Jobs\JobTypes;
use AperturePro\Domain\Tokens\TokenService;
use AperturePro\Domain\Tokens\TokenTypes;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Support\Response;

class DeliveryController
{
    public function register_routes(): void
    {
        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/delivery/zip', [
            'methods'             => 'POST',
            'callback'            => [$this, 'generate'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/delivery', [
            'methods'             => 'GET',
            'callback'            => [$this, 'show'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_view_project'],
        ]);
    }

    public function generate(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        // Enqueue job
        JobScheduler::enqueue($project_id, JobTypes::ZIP_GENERATION);

        return Response::success(['message' => 'ZIP generation started.']);
    }

    public function show(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        $zipUrl = get_post_meta($project_id, 'ap_delivery_zip_url', true);
        $zipSize = get_post_meta($project_id, 'ap_delivery_zip_size', true);
        $zipDate = get_post_meta($project_id, 'ap_delivery_zip_date', true);

        return Response::success([
            'zip_url'  => $zipUrl,
            'zip_size' => (int) $zipSize,
            'zip_date' => $zipDate,
        ]);
    }
}
