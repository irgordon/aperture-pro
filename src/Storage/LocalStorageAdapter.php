<?php

namespace AperturePro\Storage;

use AperturePro\Support\Error;

class LocalStorageAdapter implements StorageAdapterInterface
{
    protected string $baseDir;
    protected string $baseUrl;

    public function __construct()
    {
        $upload = wp_upload_dir();

        $this->baseDir = trailingslashit($upload['basedir']) . 'aperture-pro/';
        $this->baseUrl = trailingslashit($upload['baseurl']) . 'aperture-pro/';

        if (!file_exists($this->baseDir)) {
            wp_mkdir_p($this->baseDir);
        }
    }

    public function store(string $localPath, string $targetPath): string
    {
        $dest = $this->path($targetPath);

        $dir = dirname($dest);
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        if (!copy($localPath, $dest)) {
            Error::logAndThrow('Failed to store file', [
                'local' => $localPath,
                'dest'  => $dest,
            ]);
        }

        return $this->url($targetPath);
    }

    public function delete(string $targetPath): bool
    {
        $path = $this->path($targetPath);
        return file_exists($path) ? unlink($path) : false;
    }

    public function path(string $targetPath): string
    {
        return $this->baseDir . ltrim($targetPath, '/');
    }

    public function url(string $targetPath): string
    {
        return $this->baseUrl . ltrim($targetPath, '/');
    }

    public function health(): array
    {
        return [
            'adapter' => 'local',
            'writable' => is_writable($this->baseDir),
            'path' => $this->baseDir,
        ];
    }
}
