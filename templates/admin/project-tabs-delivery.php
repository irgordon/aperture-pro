<?php
$zip_path   = get_post_meta($project_id, 'ap_zip_path', true);
$zip_size   = get_post_meta($project_id, 'ap_zip_size', true);
$zip_state  = ap_get_delivery_state($project_id);
?>
<div class="ap-panel">
    <div class="ap-panel-header">
        <h3>Delivery</h3>
        <span class="ap-badge ap-badge-state"><?php echo esc_html($zip_state); ?></span>
    </div>

    <div class="ap-delivery-info">
        <?php if ($zip_path) : ?>
            <p>ZIP generated.</p>
            <p>Size: <?php echo esc_html(size_format((int) $zip_size)); ?></p>
        <?php else : ?>
            <p>No ZIP generated yet.</p>
        <?php endif; ?>
    </div>

    <div class="ap-delivery-actions">
        <button class="button button-primary"
                data-ap-action="generate-zip"
                data-project-id="<?php echo esc_attr($project_id); ?>">
            Generate ZIP
        </button>

        <button class="button"
                data-ap-action="send-delivery-link"
                data-project-id="<?php echo esc_attr($project_id); ?>">
            Send Delivery Link
        </button>
    </div>
</div>
