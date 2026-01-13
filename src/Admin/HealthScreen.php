<?php

namespace AperturePro\Admin;

use AperturePro\Core\SchemaValidator;

class HealthScreen
{
    public static function render(): void
    {
        $schemaIssues = SchemaValidator::validate();

        include APERTURE_PRO_PATH . '/templates/admin/health-dashboard.php';
    }
}
