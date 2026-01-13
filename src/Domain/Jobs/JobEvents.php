<?php

namespace AperturePro\Domain\Jobs;

class JobEvents
{
    public const FAILED      = 'aperture_pro/job.failed';
    public const RETRYING    = 'aperture_pro/job.retrying';
    public const SUCCEEDED   = 'aperture_pro/job.succeeded';
    public const DEAD_LETTER = 'aperture_pro/job.dead_letter';
}
