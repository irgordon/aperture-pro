<?php

namespace AperturePro\Domain\Delivery\Zip;

interface ZipGeneratorInterface
{
    public function generate(int $project_id): ZipResult;
}
