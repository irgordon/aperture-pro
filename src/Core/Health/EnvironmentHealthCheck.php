<?php

namespace AperturePro\Core\Health;

class EnvironmentHealthCheck implements HealthCheckInterface
{
    private $status = 'ok';
    private $message = 'Environment is healthy.';
    private $details = [];

    public function __construct()
    {
        $this->runCheck();
    }

    public function getTitle(): string
    {
        return 'Server Environment';
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
        // Check PHP Version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->status = 'error';
            $this->message = 'PHP version is too old. Please upgrade to PHP 7.4 or higher.';
            $this->details[] = 'Current PHP Version: ' . PHP_VERSION;
        } else {
            $this->details[] = 'PHP Version: ' . PHP_VERSION;
        }

        // Check Extensions
        $hasGd = extension_loaded('gd');
        $hasImagick = extension_loaded('imagick');

        if (!$hasGd && !$hasImagick) {
            $this->status = 'error';
            $this->message = 'Image processing library missing. Please install GD or Imagick.';
            $this->details[] = 'Missing: GD, Imagick';
        } else {
            $this->details[] = 'Image Library: ' . ($hasImagick ? 'Imagick' : 'GD');
        }

        // Check Uploads Directory
        $uploadDir = wp_upload_dir();
        if (!empty($uploadDir['error'])) {
            $this->status = 'error';
            $this->message = 'Uploads directory is not configured correctly.';
            $this->details[] = $uploadDir['error'];
        } elseif (!wp_is_writable($uploadDir['basedir'])) {
            $this->status = 'error';
            $this->message = 'Uploads directory is not writable.';
            $this->details[] = 'Directory: ' . $uploadDir['basedir'] . ' is not writable.';
        } else {
            $this->details[] = 'Uploads Directory: Writable';
        }
    }
}
