<?php
/**
 * Template Name: Aperture Pro Client Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<div id="aperture-pro-app">
    <main>
        <?php
        // Render the plugin’s portal shell; SPA behavior comes from client.js
        echo do_shortcode('[aperture_pro_portal]');
        ?>
    </main>
</div>
<?php
get_footer();
