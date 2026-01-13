<?php

if (!defined('ABSPATH')) {
    exit;
}

define('APERTURE_PRO_SPA_VERSION', '1.0.0');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

add_action('wp_enqueue_scripts', function () {
    // Base theme styles
    wp_enqueue_style(
        'aperture-pro-spa-style',
        get_stylesheet_uri(),
        [],
        APERTURE_PRO_SPA_VERSION
    );

    // Ensure plugin client CSS is loaded
    wp_enqueue_style(
        'aperture-pro-client',
        plugins_url('assets/client.css', WP_PLUGIN_DIR . '/aperture-pro/aperture-pro.php'),
        ['aperture-pro-components'],
        APERTURE_PRO_SPA_VERSION
    );

    // Ensure plugin client JS is loaded
    wp_enqueue_script(
        'aperture-pro-client',
        plugins_url('assets/client.js', WP_PLUGIN_DIR . '/aperture-pro/aperture-pro.php'),
        ['wp-api-fetch'],
        APERTURE_PRO_SPA_VERSION,
        true
    );

    // Optional: your own SPA shell JS
    wp_enqueue_script(
        'aperture-pro-spa-app',
        get_template_directory_uri() . '/spa-app.js',
        ['aperture-pro-client'],
        APERTURE_PRO_SPA_VERSION,
        true
    );
});

// Register a page template for the client portal
add_filter('theme_page_templates', function ($templates) {
    $templates['template-client-portal.php'] = 'Aperture Pro Client Portal';
    return $templates;
});

add_filter('template_include', function ($template) {
    if (is_page()) {
        $page_template = get_page_template_slug();
        if ($page_template === 'template-client-portal.php') {
            $custom = get_theme_file_path('template-client-portal.php');
            if (file_exists($custom)) {
                return $custom;
            }
        }
    }
    return $template;
});
