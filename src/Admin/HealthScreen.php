<?php

namespace AperturePro\Admin;

use AperturePro\Core\Health\HealthManager;

class HealthScreen
{
    public static function render(): void
    {
        $healthResults = HealthManager::getResults();

        include APERTURE_PRO_PATH . '/templates/admin/health-dashboard.php';
    }
}
