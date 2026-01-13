<?php
/** @var object[] $jobs */
?>
<div class="wrap ap-wrap">
    <h1>Jobs</h1>

    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Type</th>
            <th>Status</th>
            <th>Attempts</th>
            <th>Last Error</th>
            <th>Created</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($jobs)) : ?>
            <tr><td colspan="7">No jobs found.</td></tr>
        <?php else : ?>
            <?php foreach ($jobs as $job) : ?>
                <tr>
                    <td><?php echo esc_html($job->id); ?></td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=aperture-pro-projects&project_id=' . (int) $job->project_id)); ?>">
                            #<?php echo esc_html($job->project_id); ?>
                        </a>
                    </td>
                    <td><?php echo esc_html($job->type); ?></td>
                    <td><span class="ap-badge ap-badge-state"><?php echo esc_html($job->status); ?></span></td>
                    <td><?php echo esc_html($job->attempts . ' / ' . $job->max_attempts); ?></td>
                    <td class="ap-job-error"><?php echo esc_html($job->last_error ?: '—'); ?></td>
                    <td><?php echo esc_html($job->created_at); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
