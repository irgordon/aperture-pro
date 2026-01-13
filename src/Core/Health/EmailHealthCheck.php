<?php

namespace AperturePro\Core\Health;

class EmailHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Email system is configured.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Email System';
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
        // Basic check: does wp_mail exist?
        if (!function_exists('wp_mail')) {
            $this->status = 'error';
            $this->message = 'WordPress mail function is missing.';
            $this->details[] = 'wp_mail() function not found.';
            return;
        }

        $this->details[] = 'wp_mail() is available.';

        // Check Admin Email
        $adminEmail = get_option('admin_email');
        if (!is_email($adminEmail)) {
            $this->status = 'warning';
            $this->message = 'Invalid admin email configured.';
            $this->details[] = "Admin Email: {$adminEmail}";
        } else {
            $this->details[] = "Admin Email: {$adminEmail}";
        }
    }
}
