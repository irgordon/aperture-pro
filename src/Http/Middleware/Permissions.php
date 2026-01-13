<?php

namespace AperturePro\Http\Middleware;

use AperturePro\Domain\Tokens\TokenService;
use WP_REST_Request;

class Permissions
{
    public static function admin_can_manage_projects(): bool
    {
        return current_user_can('ap_manage_projects');
    }

    public static function admin_can_view_projects(): bool
    {
        return current_user_can('ap_view_projects');
    }

    /**
     * For REST permission_callback: admin OR client with valid token.
     */
    public static function client_or_admin_can_view_project(WP_REST_Request $request): bool
    {
        $project_id = (int) $request['id'];

        if (self::admin_can_view_projects()) {
            return true;
        }

        return TokenService::validateProjectAccessToken($project_id);
    }

    /**
     * For proofing endpoints: admin or client with valid token.
     */
    public static function client_or_admin_can_proof(WP_REST_Request $request): bool
    {
        $project_id = (int) $request['id'];

        if (self::admin_can_manage_projects()) {
            return true;
        }

        return TokenService::validateProjectAccessToken($project_id);
    }

    /**
     * Admin‑only endpoints.
     */
    public static function admin_only(): bool
    {
        return self::admin_can_manage_projects();
    }
}
