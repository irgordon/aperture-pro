<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/irgordon/aperture-pro-wordpress
 * @since             1.0.0
 * @package           Aperture_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       Aperture Pro
 * Plugin URI:        https://iangordon.app/aperturepro
 * Description:       Aperture Pro is a modern, production‑grade WordPress plugin built for photography studios that need a secure, elegant, and scalable way to deliver proofs, collect approvals, and provide final downloads. It blends a polished client experience with a robust operational backend designed for reliability, observability, and long‑term maintainability.
 * Version:           1.0.0
 * Author:            Ian Gordon
 * Author URI:        https://iangordon.app/
 * License:           MIT License
 * License URI:       https://mit-license.org/
 * Text Domain:       aperture-pro
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

/* -------------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------------- */

define('APERTURE_PRO_VERSION', '1.0.0');
define('APERTURE_PRO_FILE', __FILE__);
define('APERTURE_PRO_DIR', plugin_dir_path(__FILE__));
define('APERTURE_PRO_URL', plugin_dir_url(__FILE__));

// Backwards compatibility for existing code
define('APERTURE_PRO_PATH', APERTURE_PRO_DIR);

/* -------------------------------------------------------------------------
 * Autoload
 * ------------------------------------------------------------------------- */

$autoload = APERTURE_PRO_DIR . 'vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once APERTURE_PRO_DIR . 'inc/autoloader.php';
}

/* -------------------------------------------------------------------------
 * Activation: set one‑time setup redirect flag
 * ------------------------------------------------------------------------- */

register_activation_hook(__FILE__, function () {
    if (!current_user_can('manage_options')) {
        return;
    }

    set_transient('aperture_pro_do_setup_redirect', 1, 60);

    // Call installer activation
    if (class_exists('AperturePro\Core\Installer')) {
        \AperturePro\Core\Installer::activate();
    }
});

register_deactivation_hook(__FILE__, function () {
    if (class_exists('AperturePro\Core\Installer')) {
        \AperturePro\Core\Installer::deactivate();
    }
});

/* -------------------------------------------------------------------------
 * One‑time setup redirect (admin only)
 * ------------------------------------------------------------------------- */

add_action('admin_init', function () {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!get_transient('aperture_pro_do_setup_redirect')) {
        return;
    }

    delete_transient('aperture_pro_do_setup_redirect');

    if (
        wp_doing_ajax() ||
        defined('REST_REQUEST') ||
        wp_doing_cron() ||
        (isset($_GET['page']) && $_GET['page'] === 'aperture-pro-setup')
    ) {
        return;
    }

    wp_safe_redirect(admin_url('admin.php?page=aperture-pro-setup'));
    exit;
});

/* -------------------------------------------------------------------------
 * Plugin Initialization via Loader
 * ------------------------------------------------------------------------- */

add_action('plugins_loaded', function () {

    if (!class_exists('\AperturePro\Loader')) {
        error_log('[Aperture Pro] Loader class missing. Plugin not initialized.');
        return;
    }

    $loader = new \AperturePro\Loader(
        APERTURE_PRO_FILE,
        APERTURE_PRO_DIR,
        APERTURE_PRO_URL,
        APERTURE_PRO_VERSION
    );

    /**
     * Register all core services here.
     * These classes each implement a register() method.
     */
    $loader->register_default_services();

    /**
     * Boot all services.
     */
    $loader->boot();
});
