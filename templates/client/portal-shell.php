<?php
/** @var WP_Post $project */
/** @var int $project_id */
/** @var string $stage */
/** @var string $proof_state */
/** @var string $delivery_state */

$project_id = $project->ID;
$client_name = get_post_meta($project_id, 'ap_client_name', true);
?>
<div class="ap-client-portal" data-project-id="<?php echo esc_attr($project_id); ?>">
    <header class="ap-client-header">
        <h1><?php echo esc_html(get_the_title($project)); ?></h1>
        <?php if ($client_name) : ?>
            <p class="ap-client-subtitle">For <?php echo esc_html($client_name); ?></p>
        <?php endif; ?>
    </header>

    <main class="ap-client-main">
        <?php \AperturePro\Client\Portal::renderSection($project_id, $stage, $proof_state, $delivery_state); ?>
    </main>

    <footer class="ap-client-footer">
        <p><small>Powered by Aperture Pro</small></p>
    </footer>
</div>
