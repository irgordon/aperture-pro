<?php

namespace AperturePro\Services;

class Assets implements ServiceInterface
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style(
            'aperture-pro-components',
            APERTURE_PRO_URL . 'assets/components.css',
            [],
            APERTURE_PRO_VERSION
        );
    }
}
