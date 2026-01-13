<?php

namespace AperturePro\Domain\Jobs;

class JobTypes
{
    public const ZIP_GENERATION = 'zip_generation';
    // Future:
    // public const IMAGE_OPTIMIZATION = 'image_optimization';
    // public const EMAIL_BATCH        = 'email_batch';

    public static function all(): array
    {
        return [
            self::ZIP_GENERATION,
        ];
    }
}
