<?php

namespace AperturePro\Storage;

class StorageSettings
{
    public static function getAdapterKey(): string
    {
        return get_option('ap_storage_adapter', 'local');
    }

    public static function setAdapterKey(string $key): void
    {
        update_option('ap_storage_adapter', $key);
    }

    public static function getAvailableAdapters(): array
    {
        return [
            'local' => 'Local Storage',
            // 's3' => 'Amazon S3 (coming soon)'
        ];
    }
}
