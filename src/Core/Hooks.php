<?php

namespace AperturePro\Core;

use AperturePro\Domain\Proofing\ProofingEvents;

class Hooks
{
    public static function register(): void
    {
        // Core registration
        add_action('init', [self::class, 'registerPostTypes']);

        // existing hooks...

        add_action(ProofingEvents::SUBMITTED, [self::class, 'onProofingSubmitted'], 10, 1);
        add_action(ProofingEvents::REOPENED, [self::class, 'onProofingReopened'], 10, 1);
        add_action(ProofingEvents::COMPLETED, [self::class, 'onProofingCompleted'], 10, 1);

        // Admin UI hooks
        add_action('ap_admin_project_tabs', [self::class, 'renderAdminProofingTab'], 20, 1);
        add_action('ap_admin_project_tab_proofing', function (int $project_id) {
          include APERTURE_PRO_PATH . '/templates/admin/project-tabs-proofing.php';
        }, 10, 1);

        add_action('ap_admin_project_tab_delivery', function (int $project_id) {
          include APERTURE_PRO_PATH . '/templates/admin/project-tabs-delivery.php';
        }, 10, 1);


        // Client UI hooks
        add_action('ap_client_portal_sections', [self::class, 'renderClientProofingSection'], 20, 1);
    }

    public static function onProofingSubmitted(array $data): void
    {
        $project_id = $data['project_id'];

        ap_notify($project_id, 'admin', 'admin_proof_submitted', 'Client submitted proofing selections.');
        ap_notify($project_id, 'client', 'client_proof_submitted', 'Your selections have been submitted.');
    }

    public static function onProofingReopened(array $data): void
    {
        $project_id = $data['project_id'];

        ap_notify($project_id, 'client', 'client_proof_reopened', 'Your gallery has been reopened for changes.');
    }

    public static function onProofingCompleted(array $data): void
    {
        $project_id = $data['project_id'];

        ap_notify($project_id, 'client', 'client_editing_started', 'Your photographer has started editing your photos.');
    }

    public static function renderAdminProofingTab(int $project_id): void
    {
        $round  = \AperturePro\Domain\Proofing\ProofingRepository::getRound($project_id);
        $counts = \AperturePro\Domain\Proofing\ProofingRepository::getCounts($project_id);
        $state  = \AperturePro\Domain\Proofing\ProofingRepository::getState($project_id);
        $message= get_post_meta($project_id, 'ap_proofing_message', true);

        include APERTURE_PRO_PATH . '/templates/admin/project-proofing.php';
    }

    public static function renderClientProofingSection(int $project_id): void
    {
        $state = \AperturePro\Domain\Proofing\ProofingRepository::getState($project_id);

        include APERTURE_PRO_PATH . '/templates/client/portal-proofing.php';
    }

    public static function registerPostTypes(): void
    {
        register_post_type('ap_project', [
            'labels'      => [
                'name'          => 'Projects',
                'singular_name' => 'Project',
                'add_new'       => 'New Project',
                'add_new_item'  => 'Add New Project',
                'edit_item'     => 'Edit Project',
            ],
            'public'      => false,
            'show_ui'     => true,
            'supports'    => ['title', 'editor', 'thumbnail'],
            'menu_icon'   => 'dashicons-camera',
            'rewrite'     => false,
        ]);
    }
}
