<?php
use AperturePro\Domain\Proofing\ProofingRepository;

$state  = ProofingRepository::getState($project_id);
$round  = ProofingRepository::getRound($project_id);
$counts = ProofingRepository::getCounts($project_id);
?>
<div class="ap-panel">
    <div class="ap-panel-header">
        <h3>Proofing</h3>
        <div class="ap-proofing-meta">
            <span class="ap-badge">Round <?php echo esc_html($round); ?></span>
            <span class="ap-badge ap-badge-state"><?php echo esc_html($state); ?></span>
        </div>
    </div>

    <div class="ap-proofing-summary">
        <span>Approved: <?php echo esc_html($counts['approved']); ?></span>
        <span>Rejected: <?php echo esc_html($counts['rejected']); ?></span>
        <span>Revision: <?php echo esc_html($counts['revision']); ?></span>
        <span>Pending: <?php echo esc_html($counts['pending']); ?></span>
    </div>

    <div id="ap-proofing-gallery"
         data-project-id="<?php echo esc_attr($project_id); ?>">
        <!-- JS (admin.js) hydrates gallery via REST -->
        <div class="ap-skeleton-grid"></div>
    </div>

    <div class="ap-proofing-actions">
        <button class="button"
                data-ap-action="unlock-proofing"
                data-project-id="<?php echo esc_attr($project_id); ?>">
            Unlock for Changes
        </button>
        <button class="button button-primary"
                data-ap-action="complete-proofing"
                data-project-id="<?php echo esc_attr($project_id); ?>">
            Mark Proofing Complete
        </button>
    </div>
</div>
