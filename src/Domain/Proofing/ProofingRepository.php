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

    public static function updateImageStatus(int $project_id, int $image_id, string $status, ?string $note): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_proofing';
        $now = current_time('mysql');

        // Use INSERT ... ON DUPLICATE KEY UPDATE to preserve note if not provided
        $sql = "INSERT INTO $table (project_id, image_id, status, note, updated_at)
                VALUES (%d, %d, %s, %s, %s)
                ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                updated_at = VALUES(updated_at)";

        $params = [$project_id, $image_id, $status, $note, $now];

        if ($note !== null) {
            // Update note as well
            $sql .= ", note = VALUES(note)";
        }
        // If note is null, we do NOT update the note column, preserving existing value.

        $wpdb->query($wpdb->prepare($sql, $params));
    }

    public static function getCounts(int $project_id): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'ap_proofing';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT status, COUNT(*) as count FROM $table WHERE project_id = %d GROUP BY status",
            $project_id
        ));

        $counts = [
            'approved' => 0,
            'rejected' => 0,
            'revision' => 0,
            'pending'  => 0,
        ];

        // This only counts images that have a status.
        // To get 'pending', we need total images count.
        $totalImages = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_parent = %d
             AND post_type = 'attachment'
             AND post_mime_type LIKE %s",
            $project_id,
            'image/%'
        ));
        $ratedCount = 0;

        foreach ($results as $row) {
            $status = $row->status;
            $count = (int) $row->count;

            if (isset($counts[$status])) {
                $counts[$status] = $count;
                // If the status is NOT 'pending', it contributes to the rated count.
                if ($status !== 'pending') {
                    $ratedCount += $count;
                }
            } else {
                // Unknown status, treat as rated/handled or ignore?
                // Safest to count it as rated so we don't inflate pending.
                $ratedCount += $count;
            }
        }

        // If 'pending' was in the DB, we have its count.
        // But we also need to add images that are NOT in the DB (implicitly pending).
        $implicitPending = max(0, $totalImages - $ratedCount - $counts['pending']);

        // Total pending = Explicit Pending (from DB) + Implicit Pending (missing rows)
        $counts['pending'] += $implicitPending;

        return $counts;
    }
}
