<?php

namespace AperturePro\Admin;

class Admin
{
    public static function boot(): void
    {
        add_action('admin_menu', [self::class, 'registerMenus']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
    }

    public static function registerMenus(): void
    {
        add_menu_page(
            'Aperture Pro',
            'Aperture Pro',
            'ap_view_projects',
            'aperture-pro-projects',
            [ProjectsScreen::class, 'render'],
            'dashicons-camera',
            26
        );

        add_submenu_page(
            'aperture-pro-projects',
            'Projects',
            'Projects',
            'ap_view_projects',
            'aperture-pro-projects',
            [ProjectsScreen::class, 'render']
        );

        add_submenu_page(
            'aperture-pro-projects',
            'Jobs',
            'Jobs',
            'ap_manage_projects',
            'aperture-pro-jobs',
            [JobsScreen::class, 'render']
        );

        add_submenu_page(
            'aperture-pro-projects',
            'Health',
            'Health',
            'ap_manage_projects',
            'aperture-pro-health',
            [HealthScreen::class, 'render']
        );
    }

    public static function enqueueAssets(string $hook): void
    {
        if (strpos($hook, 'aperture-pro') === false) {
            return;
        }

        wp_enqueue_style(
            'aperture-pro-admin',
            plugins_url('assets/admin.css', APERTURE_PRO_FILE),
            [],
            APERTURE_PRO_VERSION
        );

        wp_enqueue_script(
            'aperture-pro-admin',
            plugins_url('assets/admin.js', APERTURE_PRO_FILE),
            ['wp-element', 'wp-api-fetch'],
            APERTURE_PRO_VERSION,
            true
        );

        wp_localize_script('aperture-pro-admin', 'ApertureProAdmin', [
            'restUrl' => esc_url_raw(rest_url('aperture-pro/v1/')),
            'nonce'   => wp_create_nonce('wp_rest'),
        ]);
    }
}
