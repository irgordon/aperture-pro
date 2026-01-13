<?php
/**
 * Plugin Name: Aperture Pro
 * Description: Photography SaaS proofing & delivery platform.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

require __DIR__ . '/src/Core/Autoloader.php';

AperturePro\Core\Autoloader::register('AperturePro', __DIR__ . '/src');
AperturePro\Admin\Admin::boot();

add_action('plugins_loaded', function () {
    $plugin = new AperturePro\Core\Plugin();
    $plugin->boot();
});
add_action('ap_run_job', [\AperturePro\Domain\Jobs\JobRunner::class, 'handle'], 10, 1);
add_action('rest_api_init', function () {
    (new \AperturePro\Http\Rest\ProofingController())->register_routes();
});

register_activation_hook(__FILE__, [AperturePro\Core\Installer::class, 'activate']);
register_deactivation_hook(__FILE__, [AperturePro\Core\Installer::class, 'deactivate']);
