<?php

namespace AperturePro\Admin;

use AperturePro\Domain\Jobs\JobState;

class JobsScreen
{
    public static function render(): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'ap_jobs';
        $jobs  = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 100");

        include APERTURE_PRO_PATH . '/templates/admin/jobs-list.php';
    }
}
