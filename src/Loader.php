<?php

namespace AperturePro;

use AperturePro\Services\ServiceInterface;

class Loader
{
    protected string $file;
    protected string $dir;
    protected string $url;
    protected string $version;

    /** @var array<string> */
    protected array $services = [];

    public function __construct(string $file, string $dir, string $url, string $version)
    {
        $this->file = $file;
        $this->dir = $dir;
        $this->url = $url;
        $this->version = $version;

        // Load helpers
        $helpers = $this->dir . 'src/Support/helpers.php';
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    public function register_service(string $class): void
    {
        if (!class_exists($class)) {
            error_log("[Aperture Pro] Service class {$class} not found.");
            return;
        }

        // Don't add if already added
        if (in_array($class, $this->services, true)) {
            return;
        }

        $this->services[] = $class;
    }

    public function register_default_services(): void
    {
        $this->register_service(\AperturePro\Services\CoreHooks::class);
        $this->register_service(\AperturePro\Services\Storage::class);
        $this->register_service(\AperturePro\Services\Jobs::class);
        $this->register_service(\AperturePro\Services\RestApi::class);

        if (is_admin()) {
            $this->register_service(\AperturePro\Services\Admin::class);
        }

        $this->register_service(\AperturePro\Services\Client::class);
        $this->register_service(\AperturePro\Services\Assets::class);
    }

    public function boot(): void
    {
        static $booted = false;
        if ($booted) {
            return;
        }
        $booted = true;

        foreach ($this->services as $class) {
            $service = new $class();
            if ($service instanceof ServiceInterface) {
                $service->register();
            }
        }
    }
}
