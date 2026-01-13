<?php

namespace AperturePro\Storage;

use AperturePro\Support\Error;

class StorageManager
{
    protected static ?StorageAdapterInterface $adapter = null;

    public static function boot(): void
    {
        $key = StorageSettings::getAdapterKey();

        switch ($key) {
            case 'local':
                self::$adapter = new LocalStorageAdapter();
                break;

            case 'imagekit':
                self::$adapter = new ImageKitAdapter();
                break;

            default:
                Error::logAndThrow('Unknown storage adapter: ' . $key);
        }
    }

    public static function adapter(): StorageAdapterInterface
    {
        if (!self::$adapter) {
            self::boot();
        }
        return self::$adapter;
    }
}
