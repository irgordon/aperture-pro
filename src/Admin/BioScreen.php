<?php

namespace AperturePro\Admin;

class BioScreen
{
    public static function render(): void
    {
        include APERTURE_PRO_PATH . '/templates/admin/bio-settings.php';
    }
}
