<?php

namespace AperturePro\Core\Health;

use AperturePro\Storage\StorageManager;
use AperturePro\Storage\StorageSettings;

class StorageHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Storage adapter is operational.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Storage Adapter';
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
        try {
            $adapter = StorageManager::adapter();
            $result = $adapter->health();

            if (empty($result['success']) || $result['success'] !== true) {
                $this->status = 'error';
                $this->message = 'Storage adapter reported issues.';
                $this->details = $result;
            } else {
                $this->details = [
                    'adapter' => StorageSettings::getAdapterKey(),
                    'info'    => $result,
                ];
            }
        } catch (\Throwable $e) {
            $this->status = 'error';
            $this->message = 'Failed to check storage health: ' . $e->getMessage();
        }
    }
}
