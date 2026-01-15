<?php

namespace AperturePro\Client;

use AperturePro\Core\BioSettings;
use AperturePro\Domain\Shop\PrintfulService;

class BioPage
{
    public static function boot(): void
    {
        add_action('init', [self::class, 'addRewriteRule']);
        add_filter('query_vars', [self::class, 'addQueryVar']);
        add_filter('template_include', [self::class, 'loadTemplate']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueueAssets']);
    }

    public static function addRewriteRule(): void
    {
        add_rewrite_rule('^bio/?$', 'index.php?ap_bio=1', 'top');
    }

    public static function addQueryVar($vars)
    {
        $vars[] = 'ap_bio';
        return $vars;
    }

    public static function loadTemplate($template)
    {
        if (get_query_var('ap_bio')) {
            return APERTURE_PRO_PATH . 'templates/client/bio-page.php';
        }
        return $template;
    }

    public static function enqueueAssets(): void
    {
        if (get_query_var('ap_bio')) {
            wp_enqueue_style(
                'aperture-pro-bio',
                plugins_url('assets/bio.css', APERTURE_PRO_FILE),
                [],
                APERTURE_PRO_VERSION
            );

            $settings = BioSettings::getSettings();
            $primary_color = esc_attr($settings['primaryColor']);
            $custom_css = ":root { --ap-bio-primary: {$primary_color}; }";
            wp_add_inline_style('aperture-pro-bio', $custom_css);

            wp_enqueue_script(
                'aperture-pro-bio',
                plugins_url('assets/bio.js', APERTURE_PRO_FILE),
                [],
                APERTURE_PRO_VERSION,
                true
            );
        }
    }
}
