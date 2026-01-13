<?php

namespace AperturePro\Domain\ImageOptimization;

use AperturePro\Domain\Logs\Logger;

class WpImageOptimizer implements ImageOptimizerInterface
{
    /**
     * @inheritDoc
     */
    public function optimize(int $project_id): OptimizationResult
    {
        $result = new OptimizationResult();
        $batch_size = 10; // Reduced batch size for better responsiveness
        $time_limit = 20; // 20 seconds time limit per run
        $start_time = time();
        $failed_ids = [];

        while (true) {
            // Check time limit at the start of loop
            if (time() - $start_time >= $time_limit) {
                $result->completed = false;
                break;
            }

            // Optimization: Query only unoptimized images to avoid N+1 and fetching unnecessary data
            $args = [
                'post_type'      => 'attachment',
                'post_parent'    => $project_id,
                'posts_per_page' => $batch_size,
                'post_mime_type' => 'image',
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'meta_query'     => [
                    [
                        'key'     => '_ap_optimized',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ];

            if (!empty($failed_ids)) {
                $args['post__not_in'] = $failed_ids;
            }

            $images = get_posts($args);

            if (empty($images)) {
                break; // No more images to process
            }

            foreach ($images as $image) {
                // Check time limit inside processing loop
                if (time() - $start_time >= $time_limit) {
                    $result->completed = false;
                    break 2; // Break both loops
                }

                $result->total_processed++;

                // No need to check _ap_optimized here as query filters it.

                // Track attempts
                $prev_data = get_post_meta($image->ID, '_ap_optimization_data', true);
                $attempts = isset($prev_data['attempts']) ? (int)$prev_data['attempts'] + 1 : 1;

                $file_path = get_attached_file($image->ID);

                if (!$file_path || !file_exists($file_path)) {
                    $result->errors[] = "File not found for image ID: {$image->ID}";
                    update_post_meta($image->ID, '_ap_optimized', 'error');
                    $failed_ids[] = $image->ID;
                    continue;
                }

                // Create backup path
                $backup_path = $file_path . '.bak';
                if (!copy($file_path, $backup_path)) {
                    $result->errors[] = "Could not create backup for image ID: {$image->ID}";
                    update_post_meta($image->ID, '_ap_optimized', 'error');
                    $failed_ids[] = $image->ID;
                    continue;
                }

                try {
                    $original_size = filesize($file_path);

                    // Get WordPress image editor
                    $editor = wp_get_image_editor($file_path);

                    if (is_wp_error($editor)) {
                        $result->errors[] = "WP Editor error for image ID {$image->ID}: " . $editor->get_error_message();
                        // Restore backup just in case
                        copy($backup_path, $file_path);
                        update_post_meta($image->ID, '_ap_optimized', 'error');
                        $failed_ids[] = $image->ID;
                        continue;
                    }

                    // We optimize by loading and saving with slightly reduced quality
                    // 82 is generally a good balance for web
                    $editor->set_quality(82);

                    // Save over the original file
                    $saved = $editor->save($file_path);

                    if (is_wp_error($saved)) {
                        $result->errors[] = "Failed to save optimized image ID {$image->ID}: " . $saved->get_error_message();
                        copy($backup_path, $file_path);
                        update_post_meta($image->ID, '_ap_optimized', 'error');
                        $failed_ids[] = $image->ID;
                        continue;
                    }

                    clearstatcache(false, $file_path);
                    $new_size = filesize($file_path);

                    // If the new file is larger or same size, revert to original
                    if ($new_size >= $original_size) {
                        copy($backup_path, $file_path);

                        update_post_meta($image->ID, '_ap_optimized', 'skipped');
                        update_post_meta($image->ID, '_ap_optimization_data', [
                            'status' => 'skipped',
                            'reason' => 'no_savings',
                            'original_size' => $original_size,
                            'new_size' => $new_size,
                            'timestamp' => time(),
                            'attempts' => $attempts,
                        ]);
                    } else {
                        $result->bytes_saved += ($original_size - $new_size);
                        $result->total_optimized++;

                        update_post_meta($image->ID, '_ap_optimized', 'optimized');
                        update_post_meta($image->ID, '_ap_optimization_data', [
                            'status' => 'optimized',
                            'reason' => 'saved_bytes',
                            'original_size' => $original_size,
                            'new_size' => $new_size,
                            'bytes_saved' => ($original_size - $new_size),
                            'timestamp' => time(),
                            'attempts' => $attempts,
                        ]);

                        // Regenerate metadata to ensure thumbnails are also optimized/consistent if needed.
                        // Note: This might be heavy.
                        $metadata = wp_generate_attachment_metadata($image->ID, $file_path);
                        wp_update_attachment_metadata($image->ID, $metadata);
                    }
                } catch (\Exception $e) {
                    // Restore backup on error
                    copy($backup_path, $file_path);
                    $result->errors[] = "Exception optimizing image ID {$image->ID}: " . $e->getMessage();
                    Logger::error("Optimization exception for image {$image->ID}", ['error' => $e->getMessage()], $project_id);
                    update_post_meta($image->ID, '_ap_optimized', 'error');
                    $failed_ids[] = $image->ID;
                } finally {
                    // Cleanup backup
                    if (file_exists($backup_path)) {
                        unlink($backup_path);
                    }
                }
            }
            // No offset increment as we consume the queue
        }

        return $result;
    }
}
