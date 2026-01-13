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
