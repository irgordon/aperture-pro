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
        $batch_size = 50; // Process 50 images at a time
        $offset = 0;

        while (true) {
            $images = ap_get_project_images($project_id, $batch_size, $offset);

            if (empty($images)) {
                break; // No more images to process
            }

            foreach ($images as $image) {
                $result->total_processed++;

                // Check if already optimized
                if (get_post_meta($image->ID, '_ap_optimized', true)) {
                    // Skip already optimized images
                    continue;
                }

                $file_path = get_attached_file($image->ID);

                if (!$file_path || !file_exists($file_path)) {
                    $result->errors[] = "File not found for image ID: {$image->ID}";
                    continue;
                }

                // Create backup path
                $backup_path = $file_path . '.bak';
                if (!copy($file_path, $backup_path)) {
                    $result->errors[] = "Could not create backup for image ID: {$image->ID}";
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
                        continue;
                    }

                    clearstatcache(false, $file_path);
                    $new_size = filesize($file_path);

                    // If the new file is larger or same size, revert to original
                    if ($new_size >= $original_size) {
                        copy($backup_path, $file_path);
                        // We don't mark as optimized because we might want to try again with different settings in future?
                        // Or we mark it as optimized but with 0 savings so we don't try again.
                        // For now, let's mark it so we don't loop forever if we retry the job.
                        update_post_meta($image->ID, '_ap_optimized', 1);
                    } else {
                        $result->bytes_saved += ($original_size - $new_size);
                        $result->total_optimized++;
                        update_post_meta($image->ID, '_ap_optimized', 1);

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
                } finally {
                    // Cleanup backup
                    if (file_exists($backup_path)) {
                        unlink($backup_path);
                    }
                }
            }

            $offset += $batch_size;
        }

        return $result;
    }
}
