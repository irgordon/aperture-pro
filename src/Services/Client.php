<?php

namespace AperturePro\Services;

use AperturePro\Client\Portal;
use AperturePro\Client\Gallery;
use AperturePro\Client\BioPage;

class Client implements ServiceInterface
{
    public function register(): void
    {
        Portal::boot();
        Gallery::boot();
        BioPage::boot();
    }
}
