<?php

namespace AperturePro\Services;

use AperturePro\Domain\Jobs\JobRunner;

class Jobs implements ServiceInterface
{
    public function register(): void
    {
        add_action('ap_run_job', [JobRunner::class, 'handle'], 10, 1);
    }
}
