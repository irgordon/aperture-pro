<?php

namespace AperturePro\Admin;

class ProjectsScreen
{
    public static function render(): void
    {
        $project_id = isset($_GET['project_id']) ? (int) $_GET['project_id'] : 0;

        if ($project_id > 0) {
            self::renderDetail($project_id);
        } else {
            self::renderList();
        }
    }

    protected static function renderList(): void
    {
        $projects = get_posts([
            'post_type'      => 'ap_project',
            'posts_per_page' => 50,
            'post_status'    => ['publish', 'draft'],
        ]);

        // Optimization: Pre-calculate stages to avoid N+1 queries in the template
        $stages = [];
        if (!empty($projects)) {
            global $wpdb;
            $project_ids = wp_list_pluck($projects, 'ID');
            $ids_sql     = implode(',', array_map('intval', $project_ids));

            // Batch fetch delivery status
            $deliveries = $wpdb->get_results("
                SELECT project_id, status
                FROM {$wpdb->prefix}ap_delivery
                WHERE project_id IN ($ids_sql)
            ");

            $delivery_map = [];
            foreach ($deliveries as $d) {
                $delivery_map[$d->project_id] = $d->status;
            }

            foreach ($projects as $project) {
                $pid = $project->ID;
                $stage = 'Proofing';

                // Check Proofing
                $p_state = get_post_meta($pid, 'ap_proofing_state', true);
                if ($p_state === 'submitted') {
                    $stage = 'Review';
                } elseif ($p_state === 'completed') {
                    $stage = 'Editing';
                }

                // Check Delivery
                if (isset($delivery_map[$pid])) {
                    $status = $delivery_map[$pid];
                    if ($status === 'sent') {
                        $stage = 'Delivered';
                    } elseif ($status === 'ready') {
                        $stage = 'Delivery Ready';
                    }
                }

                $stages[$pid] = $stage;
            }
        }

        include APERTURE_PRO_PATH . '/templates/admin/projects-list.php';
    }

    protected static function renderDetail(int $project_id): void
    {
        $project = get_post($project_id);
        if (!$project) {
            echo '<div class="notice notice-error"><p>Project not found.</p></div>';
            return;
        }

        $tabs = [
            'overview' => 'Overview',
            'proofing' => 'Proofing',
            'delivery' => 'Delivery',
            'activity' => 'Activity',
        ];

        $active = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'overview';
        if (!isset($tabs[$active])) {
            $active = 'overview';
        }

        include APERTURE_PRO_PATH . '/templates/admin/project-detail.php';
    }
}
