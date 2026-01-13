<?php
/** @var WP_Post $project */
/** @var array $tabs */
/** @var string $active */
$project_id = $project->ID;
?>
<div class="wrap ap-wrap ap-project-detail">
    <h1><?php echo esc_html(get_the_title($project)); ?></h1>

    <h2 class="nav-tab-wrapper ap-tabs">
        <?php foreach ($tabs as $key => $label) : ?>
            <?php
            $url = add_query_arg([
                'page'       => 'aperture-pro-projects',
                'project_id' => $project_id,
                'tab'        => $key,
            ], admin_url('admin.php'));
            ?>
            <a href="<?php echo esc_url($url); ?>"
               class="nav-tab <?php echo $active === $key ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html($label); ?>
            </a>
        <?php endforeach; ?>
    </h2>

    <div class="ap-tab-content">
        <?php
        switch ($active) {
            case 'proofing':
                do_action('ap_admin_project_tab_proofing', $project_id);
                break;

            case 'delivery':
                do_action('ap_admin_project_tab_delivery', $project_id);
                break;

            case 'activity':
                do_action('ap_admin_project_tab_activity', $project_id);
                break;

            default:
                do_action('ap_admin_project_tab_overview', $project_id);
                break;
        }
        ?>
    </div>
</div>
