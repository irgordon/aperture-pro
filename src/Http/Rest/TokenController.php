<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Domain\Tokens\TokenService;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Support\Response;

class TokenController
{
    public function register_routes(): void
    {
        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/tokens', [
            'methods'             => 'POST',
            'callback'            => [$this, 'create'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);
    }

    public function create(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];
        $type       = $req->get_param('type'); // 'access' or 'download'

        if ($type === 'download') {
            $token = TokenService::generateDownloadToken($project_id);
        } else {
            $token = TokenService::generateAccessToken($project_id);
        }

        return Response::success(['token' => $token]);
    }
}
