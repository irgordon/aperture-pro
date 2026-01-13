<?php
/** @var WP_Post[] $projects */
/** @var array $stages */
?>
<div class="wrap ap-wrap">
    <h1 class="wp-heading-inline">Projects</h1>
    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=ap_project')); ?>" class="page-title-action">Add New</a>

    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th>Project</th>
            <th>Client</th>
            <th>Status</th>
            <th>Stage</th>
            <th>Last Activity</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($projects)) : ?>
            <tr>
                <td colspan="5" class="ap-empty-state">
                    <p>No projects found.</p>
                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=ap_project')); ?>" class="button button-primary">Create your first project</a>
                </td>
            </tr>
        <?php else : ?>
            <?php foreach ($projects as $project) : ?>
                <?php
                $project_id = $project->ID;
                $client     = get_post_meta($project_id, 'ap_client_name', true);

                // Use pre-calculated stage from controller if available
                $stage      = isset($stages[$project_id]) ? $stages[$project_id] : 'Proofing';

                $status     = get_post_status($project_id);
                $last       = get_post_meta($project_id, 'ap_last_activity', true);
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=aperture-pro-projects&project_id=' . $project_id)); ?>">
                            <?php echo esc_html(get_the_title($project)); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($client ?: '—'); ?></td>
                    <td><span class="ap-badge ap-badge-status"><?php echo esc_html($status); ?></span></td>
                    <td><span class="ap-badge ap-badge-stage"><?php echo esc_html($stage); ?></span></td>
                    <td><?php echo esc_html($last ?: '—'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
