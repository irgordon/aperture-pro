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
            'limit'    => 12,
            'columns'  => 3,
            'ratio'    => 'original', // '4x5', '1x1', etc.
            'category' => '',
            'tag'      => '',
        ], $atts, 'ap_masonry_gallery');

        $args = [
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_status'    => 'inherit',
            'posts_per_page' => (int) $atts['limit'],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if (!empty($atts['category'])) {
            $args['category_name'] = sanitize_text_field($atts['category']);
        }

        if (!empty($atts['tag'])) {
            $args['tag'] = sanitize_text_field($atts['tag']);
        }

        // Filter by category or tag if provided.
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
                $full_url = wp_get_attachment_image_url(get_the_ID(), 'full');
                ?>
                <div class="ap-gallery-item">
                    <a href="<?php echo esc_url($full_url); ?>" class="ap-gallery-link">
                        <?php echo wp_get_attachment_image(get_the_ID(), 'large', false, ['loading' => 'lazy']); ?>
                    </a>
                </div>
            <?php endwhile; ?>

        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }
}
