<?php

namespace AperturePro\Admin;

class Admin
{
    public static function boot(): void
    {
        add_action('admin_menu', [self::class, 'registerMenus']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAssets']);
        add_action('admin_init', [self::class, 'redirectWizard']);
    }

    public static function redirectWizard(): void
    {
        if (get_transient('ap_wizard_pending')) {
            delete_transient('ap_wizard_pending');
            wp_safe_redirect(admin_url('admin.php?page=aperture-pro-wizard'));
            exit;
        }
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
            null, // Hidden submenu
            'Setup Wizard',
            'Setup Wizard',
            'manage_options',
            'aperture-pro-wizard',
            [WizardScreen::class, 'render']
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

        // Wizard Assets
        if (strpos($hook, 'aperture-pro-wizard') !== false) {
            wp_enqueue_style(
                'aperture-pro-wizard',
                plugins_url('assets/wizard.css', APERTURE_PRO_FILE),
                [],
                APERTURE_PRO_VERSION
            );
            wp_enqueue_script(
                'aperture-pro-wizard',
                plugins_url('assets/wizard.js', APERTURE_PRO_FILE),
                [],
                APERTURE_PRO_VERSION,
                true
            );

            // Preload current settings
            $settings = [
                'brandName'      => \AperturePro\Core\Settings::getBrandName(),
                'brandLogo'      => \AperturePro\Core\Settings::getBrandLogo(),
                'storageAdapter' => \AperturePro\Storage\StorageSettings::getAdapterKey(),
                'seoTitle'       => \AperturePro\Core\Settings::getSeoTitleTemplate(),
                'seoDesc'        => \AperturePro\Core\Settings::getSeoDescTemplate(),
                'imgQuality'     => \AperturePro\Core\Settings::getImgQuality(),
                'imgMaxWidth'    => \AperturePro\Core\Settings::getImgMaxWidth(),
            ];

            $ik = \AperturePro\Core\Settings::getImageKitConfig();
            $settings['ikPublicKey']   = $ik['publicKey'];
            $settings['ikPrivateKey']  = $ik['privateKey'];
            $settings['ikUrlEndpoint'] = $ik['urlEndpoint'];

            wp_localize_script('aperture-pro-wizard', 'ApertureProAdmin', [
                'restUrl'  => esc_url_raw(rest_url('aperture-pro/v1/')),
                'nonce'    => wp_create_nonce('wp_rest'),
                'settings' => $settings,
            ]);
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
