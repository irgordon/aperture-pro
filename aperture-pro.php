<?php
/**
 * Plugin Name: Aperture Pro
 * Description: A professional SaaS photography proofing and delivery platform.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

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
| Helpers
|--------------------------------------------------------------------------
*/
require_once APERTURE_PRO_PATH . 'src/Support/helpers.php';

/*
|--------------------------------------------------------------------------
| Activation / Deactivation
|--------------------------------------------------------------------------
*/
register_activation_hook(__FILE__, [AperturePro\Core\Installer::class, 'activate']);
register_deactivation_hook(__FILE__, [AperturePro\Core\Installer::class, 'deactivate']);

/*
|--------------------------------------------------------------------------
| Plugin Bootstrap
|--------------------------------------------------------------------------
*/
add_action('plugins_loaded', function () {

    // Core hooks (logging, events, schema validation)
    AperturePro\Core\Hooks::register();

    // Storage (must load early for Delivery + Jobs)
    AperturePro\Storage\StorageManager::boot();

    // Jobs (runner)
    add_action('ap_run_job', [AperturePro\Domain\Jobs\JobRunner::class, 'handle'], 10, 1);

    // REST API
    add_action('rest_api_init', function () {
        (new AperturePro\Http\Rest\ProjectStatusController())->register_routes();
        (new AperturePro\Http\Rest\ProofingController())->register_routes();
        (new AperturePro\Http\Rest\DeliveryController())->register_routes();
        (new AperturePro\Http\Rest\TokenController())->register_routes();
        (new AperturePro\Http\Rest\JobsController())->register_routes();
        (new AperturePro\Http\Rest\WizardController())->register_routes();
        (new AperturePro\Http\Rest\BioController())->register_routes();
    });

    // Admin UI
    if (is_admin()) {
        AperturePro\Admin\Admin::boot();
    }

    // Client Portal
    AperturePro\Client\Portal::boot();
    AperturePro\Client\Gallery::boot();
    AperturePro\Client\BioPage::boot();

    // Shared component library
    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_style('aperture-pro-components', APERTURE_PRO_URL . 'assets/components.css', [], APERTURE_PRO_VERSION);
    });

    add_action('admin_enqueue_scripts', function () {
        wp_enqueue_style('aperture-pro-components', APERTURE_PRO_URL . 'assets/components.css', [], APERTURE_PRO_VERSION);
    });
});
