<?php
/** @var AperturePro\Core\Health\HealthCheckInterface[] $healthResults */
?>
<div class="wrap ap-wrap ap-health">
    <h1 class="wp-heading-inline">System Health</h1>
    <p class="description">Monitor the status and performance of your Aperture Pro installation.</p>
    <hr class="wp-header-end">

    <div class="ap-health-grid">
        <?php foreach ($healthResults as $check) : ?>
            <?php
            $status = $check->getStatus(); // ok, warning, error, info
            $statusClass = 'ap-status-' . $status;
            $icon = 'dashicons-yes';

            if ($status === 'error') {
                $icon = 'dashicons-no-alt';
            } elseif ($status === 'warning') {
                $icon = 'dashicons-warning';
            } elseif ($status === 'info') {
                $icon = 'dashicons-info';
            }
            ?>
            <div class="ap-health-card <?php echo esc_attr($statusClass); ?>">
                <div class="ap-card-header">
                    <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                    <h2><?php echo esc_html($check->getTitle()); ?></h2>
                </div>

                <div class="ap-card-body">
                    <p class="ap-message"><?php echo esc_html($check->getMessage()); ?></p>

                    <?php
                    $details = $check->getDetails();
                    if (!empty($details)) :
                    ?>
                        <details class="ap-details">
                            <summary>Technical Details</summary>
                            <ul>
                                <?php foreach ((array)$details as $detail) : ?>
                                    <li><?php echo esc_html($detail); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
