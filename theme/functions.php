<?php

if (!defined('ABSPATH')) {
    exit;
}

define('APERTURE_PRO_STUDIO_VERSION', '1.0.0');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('editor-styles');
});

add_action('wp_enqueue_scripts', function () {
    // Theme base styles
    wp_enqueue_style(
        'aperture-pro-studio-style',
        get_stylesheet_uri(),
        [],
        APERTURE_PRO_STUDIO_VERSION
    );

    // Shared components from plugin (badges, toasts, etc.)
    wp_enqueue_style(
        'aperture-pro-components',
        plugins_url('assets/components.css', WP_PLUGIN_DIR . '/aperture-pro/aperture-pro.php'),
        [],
        APERTURE_PRO_STUDIO_VERSION
    );

    // Optional: reuse client.css for gallery styling
    wp_enqueue_style(
        'aperture-pro-client',
        plugins_url('assets/client.css', WP_PLUGIN_DIR . '/aperture-pro/aperture-pro.php'),
        ['aperture-pro-components'],
        APERTURE_PRO_STUDIO_VERSION
    );

    // Studio SPA app
    wp_enqueue_script(
        'aperture-pro-studio-app',
        get_template_directory_uri() . '/studio-app.js',
        ['wp-api-fetch'],
        APERTURE_PRO_STUDIO_VERSION,
        true
    );

    wp_localize_script('aperture-pro-studio-app', 'ApertureProStudio', [
        'restUrl' => esc_url_raw(rest_url('aperture-pro/v1/')),
    ]);

    // Enqueue Google reCAPTCHA
    wp_enqueue_script(
        'google-recaptcha',
        'https://www.google.com/recaptcha/api.js',
        [],
        null,
        true
    );
});

// Add async defer to reCAPTCHA
add_filter('script_loader_tag', function ($tag, $handle) {
    if ($handle === 'google-recaptcha') {
        return str_replace(' src', ' async defer src', $tag);
    }
    return $tag;
}, 10, 2);
