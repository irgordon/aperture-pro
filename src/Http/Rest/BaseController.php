<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use WP_Error;
use AperturePro\Domain\Logs\Logger;

abstract class BaseController
{
    /**
     * Register a REST route with exception handling.
     *
     * @param string $namespace The route namespace (e.g., 'aperture-pro/v1').
     * @param string $route     The route regex.
     * @param array  $args      Route arguments (methods, callback, permission_callback).
     */
    protected function register_route(string $namespace, string $route, array $args): void
    {
        if (isset($args['callback']) && is_callable($args['callback'])) {
            $originalCallback = $args['callback'];
            $args['callback'] = function (WP_REST_Request $request) use ($originalCallback) {
                try {
                    return call_user_func($originalCallback, $request);
                } catch (\Throwable $e) {
                    Logger::error('REST API Exception', [
                        'message' => $e->getMessage(),
                        'file'    => $e->getFile(),
                        'line'    => $e->getLine(),
                        'trace'   => $e->getTraceAsString(),
                    ]);

                    return new WP_Error(
                        'ap_internal_server_error',
                        'An unexpected error occurred while processing your request.',
                        [
                            'status' => 500,
                            'error'  => defined('WP_DEBUG') && WP_DEBUG ? $e->getMessage() : null,
                        ]
                    );
                }
            };
        }

        register_rest_route($namespace, $route, $args);
    }
}
