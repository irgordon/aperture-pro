<?php
/**
 * Plugin Name: Aperture Pro
 * Description: A professional photography proofing and delivery platform.
 * Version: 1.0.0
 * Author: Aperture Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Constants
|--------------------------------------------------------------------------
*/

define('APERTURE_PRO_FILE', __FILE__);
define('APERTURE_PRO_PATH', plugin_dir_path(__FILE__));
define('APERTURE_PRO_URL', plugin_dir_url(__FILE__));
define('APERTURE_PRO_VERSION', '1.0.0');

/*
|--------------------------------------------------------------------------
| Autoloader
|--------------------------------------------------------------------------
*/

require_once APERTURE_PRO_PATH . 'src/Core/Autoloader.php';

AperturePro\Core\Autoloader::register('AperturePro', APERTURE_PRO_PATH . 'src');

/*
|--------------------------------------------------------------------------
| Activation / Deactivation
|--------------------------------------------------------------------------
*/

register_activation_hook(__FILE__, function () {
    AperturePro\Core\Installer::activate();
});

register_deactivation_hook(__FILE__, function () {
    AperturePro\Core\Installer::deactivate();
});

/*
|--------------------------------------------------------------------------
| Plugin Bootstrap
|--------------------------------------------------------------------------
*/

add_action('plugins_loaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Core Services
    |--------------------------------------------------------------------------
    */

    // Schema validator, installer, logging, utilities
    AperturePro\Core\Hooks::register();

    /*
    |--------------------------------------------------------------------------
    | Domain Slices
    |--------------------------------------------------------------------------
    */

    // Tokens (access + download)
    // No explicit boot needed — used by middleware + controllers

    // Jobs (scheduler + runner)
    add_action('ap_run_job', [AperturePro\Domain\Jobs\JobRunner::class, 'handle'], 10, 1);

    // Proofing slice
    // No explicit boot — REST + Admin UI hooks load it

    // Delivery slice
    // DeliveryService is used by JobRunner + REST

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */

    // Permissions, StateValidation, Input helpers are autoloaded automatically

    /*
    |--------------------------------------------------------------------------
    | REST API
    |--------------------------------------------------------------------------
    */

    add_action('rest_api_init', function () {
        (new AperturePro\Http\Rest\ProjectStatusController())->register_routes();
        (new AperturePro\Http\Rest\ProofingController())->register_routes();
        (new AperturePro\Http\Rest\DeliveryController())->register_routes();
        (new AperturePro\Http\Rest\TokenController())->register_routes();
        (new AperturePro\Http\Rest\JobsController())->register_routes();
    });

    /*
    |--------------------------------------------------------------------------
    | Admin UI
    |--------------------------------------------------------------------------
    */

    if (is_admin()) {
        AperturePro\Admin\Admin::boot();
    }

    /*
    |--------------------------------------------------------------------------
    | Client Portal
    |--------------------------------------------------------------------------
    */

    AperturePro\Client\Portal::boot();

    /*
    |--------------------------------------------------------------------------
    | Assets (Shared Component Library)
    |--------------------------------------------------------------------------
    */

    add_action('wp_enqueue_scripts', function () {
        wp_register_style(
            'aperture-pro-components',
            APERTURE_PRO_URL . 'assets/components.css',
            [],
            APERTURE_PRO_VERSION
        );
    });

    add_action('admin_enqueue_scripts', function () {
        wp_enqueue_style('aperture-pro-components');
    });

    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_style('aperture-pro-components');
    });
});
