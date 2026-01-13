<?php

namespace AperturePro\Client;

class Gallery
{
    public static function boot(): void
    {
        add_shortcode('ap_masonry_gallery', [self::class, 'renderShortcode']);
    }

    public static function renderShortcode($atts = []): string
    {
        $atts = shortcode_atts([
            'limit'   => 12,
            'columns' => 3,
            'ratio'   => 'original', // '4x5', '1x1', etc.
        ], $atts, 'ap_masonry_gallery');

        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => (int) $atts['limit'],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        // In a real app, we might filter by a "portfolio" tag or category.
        // For now, we just grab recent images.
        $query = new \WP_Query($args);

        if (!$query->have_posts()) {
            return '<p>No images found.</p>';
        }

        ob_start();
        ?>
        <div class="ap-masonry-gallery"
             style="--ap-gallery-columns: <?php echo esc_attr($atts['columns']); ?>;"
             data-ratio="<?php echo esc_attr($atts['ratio']); ?>">

            <?php while ($query->have_posts()): $query->the_post(); ?>
                <?php
                $img_url = wp_get_attachment_image_url(get_the_ID(), 'large');
                $full_url = wp_get_attachment_image_url(get_the_ID(), 'full');
                $alt = get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true) ?: get_the_title();
                ?>
                <div class="ap-gallery-item">
                    <a href="<?php echo esc_url($full_url); ?>" class="ap-gallery-link">
                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                    </a>
                </div>
            <?php endwhile; ?>

        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }
}
