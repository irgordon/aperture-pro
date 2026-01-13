<?php

namespace AperturePro\Core;

class Autoloader
{
    public static function register(string $prefix, string $baseDir): void
    {
        spl_autoload_register(function ($class) use ($prefix, $baseDir) {
            if (strpos($class, $prefix . '\\') !== 0) return;
            $relative = substr($class, strlen($prefix) + 1);
            $file = $baseDir . '/' . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) require $file;
        });
    }
}
