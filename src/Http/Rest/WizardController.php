<?php

namespace AperturePro\Http\Rest;

use AperturePro\Core\Settings;
use WP_REST_Request;
use WP_REST_Response;

class WizardController extends BaseController
{
    public function register_routes(): void
    {
        $this->register_route('aperture-pro/v1', '/wizard/save', [
            'methods'  => 'POST',
            'callback' => [$this, 'save'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);
    }

    public function save(WP_REST_Request $request): WP_REST_Response
    {
        $data = $request->get_json_params();

        // Save generic settings
        Settings::updateSettings($data);

        // Mark wizard as completed
        Settings::markWizardCompleted();

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Settings saved and wizard completed.',
        ], 200);
    }
}
