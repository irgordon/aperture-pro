<?php

namespace AperturePro\Http\Middleware;

use WP_Error;
use AperturePro\Domain\Project\ProjectStage;
use AperturePro\Domain\Proofing\ProofingRepository;
use AperturePro\Domain\Editing\EditingService;
use AperturePro\Domain\Delivery\DeliveryService;

class StateValidation
{
    public static function assertStage(int $project_id, array $allowed): void
    {
        $stage = ProjectStage::get($project_id);

        if (!in_array($stage, $allowed, true)) {
            throw new WP_Error(
                'ap_invalid_stage',
                'This action is not allowed in the current project stage.',
                ['status' => 400]
            );
        }
    }

    public static function assertProofingState(int $project_id, array $allowed): void
    {
        $state = ProofingRepository::getState($project_id);

        if (!in_array($state, $allowed, true)) {
            throw new WP_Error(
                'ap_invalid_proofing_state',
                'This action is not allowed in the current proofing state.',
                ['status' => 400]
            );
        }
    }

    public static function assertEditingState(int $project_id, array $allowed): void
    {
        $state = EditingService::getState($project_id);

        if (!in_array($state, $allowed, true)) {
            throw new WP_Error(
                'ap_invalid_editing_state',
                'This action is not allowed in the current editing state.',
                ['status' => 400]
            );
        }
    }

    public static function assertDeliveryState(int $project_id, array $allowed): void
    {
        $state = DeliveryService::getState($project_id);

        if (!in_array($state, $allowed, true)) {
            throw new WP_Error(
                'ap_invalid_delivery_state',
                'This action is not allowed in the current delivery state.',
                ['status' => 400]
            );
        }
    }
}
