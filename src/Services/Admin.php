<?php

namespace AperturePro\Services;

use AperturePro\Admin\Admin as AdminCore;

class Admin implements ServiceInterface
{
    public function register(): void
    {
        AdminCore::boot();
    }
}
