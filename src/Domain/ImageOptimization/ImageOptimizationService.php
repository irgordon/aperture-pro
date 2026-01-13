<?php

namespace AperturePro\Domain\ImageOptimization;

use AperturePro\Domain\Logs\Logger;

class ImageOptimizationService
{
    public static function getOptimizer(): ImageOptimizerInterface
    {
        // In the future we could switch implementations here (e.g. TinyPNG, ImageMagick direct, etc.)
        return new WpImageOptimizer();
    }

    public static function handleSuccess(int $project_id, OptimizationResult $result): void
    {
        update_post_meta($project_id, 'ap_optimization_date', current_time('mysql'));
        update_post_meta($project_id, 'ap_optimization_stats', [
            'processed' => $result->total_processed,
            'optimized' => $result->total_optimized,
            'saved_bytes' => $result->bytes_saved,
            'errors' => count($result->errors)
        ]);

        Logger::info('Image optimization job completed', [
            'processed' => $result->total_processed,
            'optimized' => $result->total_optimized,
            'saved_bytes' => $result->bytes_saved,
            'errors' => $result->errors,
        ], $project_id);
    }
}
