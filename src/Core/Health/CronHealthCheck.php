<?php

namespace AperturePro\Core\Health;

class CronHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Cron system is operational.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Background Processing (Cron)';
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDetails()
    {
        return $this->details;
    }

    private function runCheck(): void
    {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            $this->status = 'warning';
            $this->message = 'WP Cron is disabled via configuration.';
            $this->details['note'] = 'Ensure you have a system cron configured to call wp-cron.php';
        }

        $hook = 'ap_run_job';
        $next = wp_next_scheduled($hook);

        if ($next) {
            $delay = time() - $next;
            $this->details['next_run'] = date('Y-m-d H:i:s', $next);

            if ($delay > 600) { // 10 minutes overdue
                $this->status = 'error';
                $this->message = 'Background jobs are stuck. Last scheduled run was ' . human_time_diff($next) . ' ago.';
            }
        } else {
            // Check if we have pending jobs
            global $wpdb;
            $table = $wpdb->prefix . 'ap_jobs';

            // Safety check if table exists
            if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) {
                $pending = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status IN ('pending', 'processing')");

                if ($pending > 0) {
                    $this->status = 'error';
                    $this->message = 'There are ' . $pending . ' pending jobs but no background process is scheduled.';
                } else {
                     $this->details['info'] = 'No active schedule, but no pending jobs.';
                }
            }
        }
    }
}
