<?php

namespace AperturePro\Core\Health;

class DiskSpaceHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Disk space is sufficient.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Disk Space';
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
        $uploadDir = wp_upload_dir();
        $path = $uploadDir['basedir'];

        if (!file_exists($path)) {
             $path = ABSPATH;
        }

        $free = disk_free_space($path);
        $total = disk_total_space($path);

        if ($free === false) {
            $this->status = 'warning';
            $this->message = 'Could not determine disk space.';
            return;
        }

        $freeMb = round($free / 1024 / 1024, 2);
        $totalMb = round($total / 1024 / 1024, 2);
        $percentFree = $total > 0 ? round(($free / $total) * 100, 2) : 0;

        $this->details = [
            'path' => $path,
            'free_mb' => $freeMb,
            'total_mb' => $totalMb,
            'percent_free' => $percentFree . '%',
        ];

        if ($freeMb < 500) { // Less than 500MB
             $this->status = 'warning';
             $this->message = 'Low disk space (' . $freeMb . ' MB free).';
        }

        if ($freeMb < 100) { // Less than 100MB
             $this->status = 'error';
             $this->message = 'Critical low disk space (' . $freeMb . ' MB free).';
        }
    }
}
