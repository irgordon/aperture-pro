<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use WP_Error;
use AperturePro\Domain\Delivery\DeliveryRepository;
use AperturePro\Domain\Jobs\JobScheduler;
use AperturePro\Domain\Jobs\JobTypes;
use AperturePro\Domain\Tokens\TokenService;
use AperturePro\Domain\Tokens\TokenTypes;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Support\Response;
use AperturePro\Support\Cache;

class DeliveryController extends BaseController
{
    public function register_routes(): void
    {
        $this->register_route('aperture-pro/v1', '/projects/(?P<id>\d+)/delivery/zip', [
            'methods'             => 'POST',
            'callback'            => [$this, 'generate'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        $this->register_route('aperture-pro/v1', '/projects/(?P<id>\d+)/delivery', [
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

        Cache::forget(Cache::key('delivery', $project_id));
        Cache::forget(Cache::key('status', $project_id));

        return Response::success(['message' => 'ZIP generation started.']);
    }

    public function show(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        return Cache::remember(
            Cache::key('delivery', $project_id),
            function () use ($project_id) {
                $delivery = DeliveryRepository::get($project_id);

                if (!$delivery) {
                     return Response::success([
                        'zip_url'  => null,
                        'zip_size' => 0,
                        'zip_date' => null,
                    ]);
                }

                return Response::success([
                    'zip_url'  => $delivery->zip_path,
                    'zip_size' => (int) $delivery->zip_size,
                    'zip_date' => $delivery->updated_at,
                ]);
            },
            300
        );
    }
}
