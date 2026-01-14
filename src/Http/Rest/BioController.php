<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Core\BioSettings;
use AperturePro\Http\Middleware\Permissions;

class BioController extends BaseController
{
    public function register_routes(): void
    {
        $this->register_route('aperture-pro/v1', '/bio/settings', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_settings'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        $this->register_route('aperture-pro/v1', '/bio/settings', [
            'methods'             => 'POST',
            'callback'            => [$this, 'update_settings'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);
    }

    public function get_settings(WP_REST_Request $req)
    {
        return BioSettings::getSettings();
    }

    public function update_settings(WP_REST_Request $req)
    {
        $params = $req->get_json_params();
        BioSettings::updateSettings($params);

        return $this->response([
            'success' => true,
            'settings' => BioSettings::getSettings()
        ]);
    }
}
