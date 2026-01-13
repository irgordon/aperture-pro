<?php

namespace AperturePro\Domain\Delivery\Zip;

class ZipResult
{
    public string $path;
    public int $size;
    public bool $is_remote;

    public function __construct(string $path, int $size, bool $is_remote)
    {
        $this->path      = $path;
        $this->size      = $size;
        $this->is_remote = $is_remote;
    }
}
