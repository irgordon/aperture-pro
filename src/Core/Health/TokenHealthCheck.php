<?php

namespace AperturePro\Core\Health;

class TokenHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Token system is functioning.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Access Tokens';
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
        global $wpdb;
        $table = $wpdb->prefix . 'ap_tokens';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            $this->status = 'error';
            $this->message = 'Tokens table missing.';
            return;
        }

        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        $expired = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE expires_at < NOW()");
        $used = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE used = 1");

        $this->details = [
            'total' => $total,
            'expired' => $expired,
            'used' => $used,
        ];
    }
}
