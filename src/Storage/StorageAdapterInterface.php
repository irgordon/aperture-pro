<?php

namespace AperturePro\Storage;

interface StorageAdapterInterface
{
    /**
     * Store a file and return its public URL.
     */
    public function store(string $localPath, string $targetPath): string;

    /**
     * Delete a stored file.
     */
    public function delete(string $targetPath): bool;

    /**
     * Return the absolute filesystem path for a stored file.
     */
    public function path(string $targetPath): string;

    /**
     * Return the public URL for a stored file.
     */
    public function url(string $targetPath): string;

    /**
     * Whether this adapter is currently operational.
     */
    public function health(): array;
}
