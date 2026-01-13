<?php
use AperturePro\Domain\Proofing\ProofingRepository;

$counts = ProofingRepository::getCounts($project_id);
$message = get_post_meta($project_id, 'ap_proofing_message', true);
?>
<section class="ap-client-section ap-client-proofing">
    <h2>Proof Your Photos</h2>

    <?php if ($message) : ?>
        <div class="ap-client-note">
            <?php echo wpautop(esc_html($message)); ?>
        </div>
    <?php endif; ?>

    <div class="ap-client-proofing-summary">
        <span>Approved: <?php echo esc_html($counts['approved']); ?></span>
        <span>Rejected: <?php echo esc_html($counts['rejected']); ?></span>
        <span>Revision: <?php echo esc_html($counts['revision']); ?></span>
        <span>Pending: <?php echo esc_html($counts['pending']); ?></span>
    </div>

    <div id="ap-client-proofing-gallery"
         data-project-id="<?php echo esc_attr($project_id); ?>">
        <!-- client.js will hydrate this; fallback text: -->
        <p>Loading your gallery…</p>
    </div>

    <div class="ap-client-proofing-actions">
        <button class="ap-btn-primary"
                data-ap-client-action="submit-proofing"
                data-project-id="<?php echo esc_attr($project_id); ?>">
            Submit Selections
        </button>
    </div>
</section>
