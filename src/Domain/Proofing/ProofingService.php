<?php

namespace AperturePro\Domain\Proofing;

use AperturePro\Domain\Logs\Logger;
use AperturePro\Domain\Project\ProjectStage;

class ProofingService
{
    public static function updateImage(int $project_id, int $image_id, string $status, ?string $note): void
    {
        update_post_meta($image_id, 'ap_proof_status', $status);

        if ($note !== null && $note !== '') {
            update_post_meta($image_id, 'ap_proof_note', wp_kses_post($note));
        }

        do_action(ProofingEvents::IMAGE_UPDATED, [
            'project_id' => $project_id,
            'image_id'   => $image_id,
            'status'     => $status,
        ]);

        Logger::info('Proofing image updated', [
            'image_id' => $image_id,
            'status'   => $status,
        ], $project_id);
    }

    public static function submitSelections(int $project_id, ?string $message): void
    {
        ProofingRepository::setState($project_id, ProofingState::SUBMITTED);

        if ($message !== null && $message !== '') {
            update_post_meta($project_id, 'ap_proofing_message', wp_kses_post($message));
        }

        do_action(ProofingEvents::SUBMITTED, [
            'project_id' => $project_id,
            'message'    => $message,
        ]);

        Logger::info('Client submitted proofing selections', [
            'message' => $message,
        ], $project_id);
    }

    public static function reopen(int $project_id): void
    {
        ProofingRepository::incrementRound($project_id);
        ProofingRepository::setState($project_id, ProofingState::REOPENED);

        do_action(ProofingEvents::REOPENED, [
            'project_id' => $project_id,
            'round'      => ProofingRepository::getRound($project_id),
        ]);

        Logger::warning('Proofing reopened', [], $project_id);
    }

    public static function complete(int $project_id): void
    {
        ProofingRepository::setState($project_id, ProofingState::SUBMITTED);
        ProjectStage::set($project_id, ProjectStage::EDITING);

        do_action(ProofingEvents::COMPLETED, [
            'project_id' => $project_id,
        ]);

        Logger::info('Proofing completed; editing started', [], $project_id);
    }
}
