<?php

namespace AperturePro\Domain\ImageOptimization;

interface ImageOptimizerInterface
{
    /**
     * Optimizes all images associated with a project.
     *
     * @param int $project_id
     * @return OptimizationResult
     */
    public function optimize(int $project_id): OptimizationResult;
}
