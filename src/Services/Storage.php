<?php

namespace AperturePro\Services;

use AperturePro\Storage\StorageManager;

class Storage implements ServiceInterface
{
    public function register(): void
    {
        StorageManager::boot();
    }
}
