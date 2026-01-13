<?php
$client_name  = get_post_meta($project_id, 'ap_client_name', true);
$client_email = get_post_meta($project_id, 'ap_client_email', true);
$status       = get_post_status($project_id);
$stage        = ap_get_stage($project_id);
$last_activity = get_post_meta($project_id, 'ap_last_activity', true);
?>
<div class="ap-overview-grid">
    <div class="ap-panel">
        <div class="ap-panel-header">
            <h3>Client Details</h3>
        </div>
        <div class="ap-form-group">
            <label for="ap-client-name">Name</label>
            <input type="text" id="ap-client-name" name="ap_client_name" value="<?php echo esc_attr($client_name); ?>" class="widefat">
        </div>
        <div class="ap-form-group">
            <label for="ap-client-email">Email</label>
            <input type="email" id="ap-client-email" name="ap_client_email" value="<?php echo esc_attr($client_email); ?>" class="widefat">
        </div>
        <div class="ap-form-actions">
            <button class="button button-primary" id="ap-save-client-details" data-project-id="<?php echo esc_attr($project_id); ?>">Save Client Details</button>
        </div>
    </div>

    <div class="ap-panel">
        <div class="ap-panel-header">
            <h3>Project Status</h3>
        </div>
        <div class="ap-status-row">
            <strong>Status:</strong> <span class="ap-badge ap-badge-status"><?php echo esc_html($status); ?></span>
        </div>
        <div class="ap-status-row">
            <strong>Stage:</strong> <span class="ap-badge ap-badge-stage"><?php echo esc_html($stage); ?></span>
        </div>
        <div class="ap-status-row">
            <strong>Last Activity:</strong> <span><?php echo esc_html($last_activity ?: 'No activity yet'); ?></span>
        </div>
    </div>
</div>
