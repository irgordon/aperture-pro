<?php

namespace AperturePro\Domain\Project;

use AperturePro\Domain\Proofing\ProofingService;
use AperturePro\Domain\Editing\EditingService;
use AperturePro\Domain\Delivery\DeliveryService;

class ProjectStatusService
{
    public function get_status(int $project_id): array
    {
        $stage          = ProjectStage::get($project_id);
        $proof          = ProofingService::getState($project_id);
        $editing        = EditingService::getState($project_id);
        $delivery       = DeliveryService::getState($project_id);
        $proofCounts    = ProofingService::getCounts($project_id);
        $proofRound     = ProofingService::getRound($project_id);
        $zip            = DeliveryService::getZipMeta($project_id);
        $tokenStatus    = DeliveryService::getTokenStatus($project_id);

        return [
            'id'       => $project_id,
            'stage'    => $stage,
            'phase'    => ProjectStage::computePhase($stage, $proof, $editing, $delivery),
            'proofing' => [
                'state'  => $proof,
                'round'  => $proofRound,
                'counts' => $proofCounts,
            ],
            'editing'  => [
                'state' => $editing,
            ],
            'delivery' => [
                'state' => $delivery,
                'zip'   => $zip,
            ],
            'access'   => [
                'token' => $tokenStatus,
            ],
            'next_actions' => ProjectStage::computeNextActions($stage, $proof, $editing, $delivery),
        ];
    }
}
