<?php

namespace AperturePro\Domain\Proofing;

class ProofingRepository
{
    public static function getState(int $project_id): string
    {
        $state = get_post_meta($project_id, 'ap_proofing_state', true);
        return $state ?: ProofingState::OPEN;
    }

    public static function setState(int $project_id, string $state): void
    {
        update_post_meta($project_id, 'ap_proofing_state', $state);
    }

    public static function getRound(int $project_id): int
    {
        $round = (int) get_post_meta($project_id, 'ap_proofing_round', true);
        return $round > 0 ? $round : 1;
    }

    public static function incrementRound(int $project_id): void
    {
        $round = self::getRound($project_id);
        update_post_meta($project_id, 'ap_proofing_round', $round + 1);
    }

    public static function getCounts(int $project_id): array
    {
        $images = ap_get_project_images($project_id); // your helper

        $counts = [
            'approved' => 0,
            'rejected' => 0,
            'revision' => 0,
            'pending'  => 0,
        ];

        foreach ($images as $img) {
            $status = get_post_meta($img->ID, 'ap_proof_status', true);

            if (!$status) {
                $counts['pending']++;
                continue;
            }

            if (isset($counts[$status])) {
                $counts[$status]++;
            } else {
                $counts['pending']++;
            }
        }

        return $counts;
    }
}
