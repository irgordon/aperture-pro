<?php

namespace AperturePro\Services;

use AperturePro\Core\Hooks;

class CoreHooks implements ServiceInterface
{
    public function register(): void
    {
        Hooks::register();
    }
}
