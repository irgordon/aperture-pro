<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<div id="aperture-pro-app">
    <main>
        <?php
        // Fallback: render normal content if not using the portal template
        if (have_posts()) :
            while (have_posts()) : the_post();
                the_content();
            endwhile;
        endif;
        ?>
    </main>
</div>
<?php
get_footer();
