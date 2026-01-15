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
                    'key'     => '_ap_optimization_data',
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

                // Track attempts
                $prev_data = get_post_meta($image->ID, '_ap_optimization_data', true);
                $attempts = isset($prev_data['attempts']) ? (int)$prev_data['attempts'] + 1 : 1;

                $file_path = get_attached_file($image->ID);

                // Main Image Optimization
                try {
                    $saved_bytes = $this->optimizeFile($file_path);

                    if ($saved_bytes > 0) {
                        $result->bytes_saved += $saved_bytes;
                        $result->total_optimized++;

                        update_post_meta($image->ID, '_ap_optimization_data', [
                            'status' => 'optimized',
                            'reason' => 'saved_bytes',
                            'original_size' => filesize($file_path) + $saved_bytes,
                            'new_size' => filesize($file_path),
                            'bytes_saved' => $saved_bytes,
                            'timestamp' => time(),
                            'attempts' => $attempts,
                        ]);

                        // Optimize thumbnails
                        // We iterate existing thumbnails instead of regenerating them to save performance.
                        $metadata = wp_get_attachment_metadata($image->ID);
                        if ($metadata && isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                            $dir = dirname($file_path);
                            foreach ($metadata['sizes'] as $size => $size_info) {
                                if (empty($size_info['file'])) continue;

                                $thumb_path = $dir . DIRECTORY_SEPARATOR . $size_info['file'];

                                try {
                                    $thumb_saved = $this->optimizeFile($thumb_path);
                                    if ($thumb_saved > 0) {
                                        $result->bytes_saved += $thumb_saved;
                                    }
                                } catch (\Exception $e) {
                                    // Log warning but don't fail the whole image optimization
                                    Logger::warning("Failed to optimize thumbnail {$size} for image {$image->ID}", ['error' => $e->getMessage()], $project_id);
                                }
                            }
                        }

                    } else {
                        // Skipped (no savings)
                        update_post_meta($image->ID, '_ap_optimization_data', [
                            'status' => 'skipped',
                            'reason' => 'no_savings',
                            'original_size' => filesize($file_path),
                            'new_size' => filesize($file_path),
                            'timestamp' => time(),
                            'attempts' => $attempts,
                        ]);
                    }

                } catch (\Exception $e) {
                    $result->errors[] = "Exception optimizing image ID {$image->ID}: " . $e->getMessage();
                    Logger::error("Optimization exception for image {$image->ID}", ['error' => $e->getMessage()], $project_id);
                    update_post_meta($image->ID, '_ap_optimization_data', [
                        'status' => 'error',
                        'reason' => 'exception',
                        'error_message' => $e->getMessage(),
                        'timestamp' => time(),
                        'attempts' => $attempts,
                    ]);
                    $failed_ids[] = $image->ID;
                }
            }
        }

        return $result;
    }

    /**
     * Optimizes a single image file.
     *
     * @param string $file_path
     * @return int Bytes saved (positive) or 0 if skipped/no savings.
     * @throws \Exception If optimization fails.
     */
    private function optimizeFile(string $file_path): int
    {
        if (!$file_path || !file_exists($file_path)) {
            throw new \Exception("File not found: " . $file_path);
        }

        $backup_path = $file_path . '.bak';
        if (!copy($file_path, $backup_path)) {
            throw new \Exception("Could not create backup");
        }

        try {
            $original_size = filesize($file_path);
            $editor = wp_get_image_editor($file_path);

            if (is_wp_error($editor)) {
                throw new \Exception("WP Editor error: " . $editor->get_error_message());
            }

            // We optimize by loading and saving with slightly reduced quality
            // 82 is generally a good balance for web
            $editor->set_quality(82);

            $saved = $editor->save($file_path);

            if (is_wp_error($saved)) {
                throw new \Exception("Failed to save: " . $saved->get_error_message());
            }

            clearstatcache(false, $file_path);
            $new_size = filesize($file_path);

            // If the new file is larger or same size, revert to original
            if ($new_size >= $original_size) {
                copy($backup_path, $file_path);
                return 0; // Skipped
            }

            return ($original_size - $new_size);

        } catch (\Exception $e) {
            // Restore backup on error
            if (file_exists($backup_path)) {
                copy($backup_path, $file_path);
            }
            throw $e;
        } finally {
            // Cleanup backup
            if (file_exists($backup_path)) {
                unlink($backup_path);
            }
        }
    }
}
