<?php

namespace AperturePro\Domain\ImageOptimization;

class OptimizationResult
{
    /** @var int */
    public $total_processed = 0;

    /** @var int */
    public $total_optimized = 0;

    /** @var int */
    public $bytes_saved = 0;

    /** @var array */
    public $errors = [];

    /**
     * @param int $total_processed
     * @param int $total_optimized
     * @param int $bytes_saved
     * @param array $errors
     */
    public function __construct(int $total_processed = 0, int $total_optimized = 0, int $bytes_saved = 0, array $errors = [])
    {
        $this->total_processed = $total_processed;
        $this->total_optimized = $total_optimized;
        $this->bytes_saved = $bytes_saved;
        $this->errors = $errors;
    }
}
