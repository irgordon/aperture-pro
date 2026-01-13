<?php
$zip_url  = get_post_meta($project_id, 'ap_zip_url', true);
$zip_size = get_post_meta($project_id, 'ap_zip_size', true);
?>
<section class="ap-client-section ap-client-delivery">
    <h2>Your Final Photos</h2>

    <?php if ($zip_url) : ?>
        <p>Your gallery is ready to download.</p>
        <p>
            <a class="ap-btn-primary" href="<?php echo esc_url($zip_url); ?>">
                Download All Photos
            </a>
        </p>
        <?php if ($zip_size) : ?>
            <p class="ap-client-meta">Approximate size: <?php echo esc_html(size_format((int) $zip_size)); ?></p>
        <?php endif; ?>
    <?php else : ?>
        <p>Your photos are not ready for download yet. You’ll receive an email as soon as they’re available.</p>
    <?php endif; ?>
</section>
