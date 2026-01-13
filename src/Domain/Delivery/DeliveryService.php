<?php

namespace AperturePro\Domain\Delivery;

use AperturePro\Domain\Delivery\Zip\ZipGeneratorInterface;
use AperturePro\Domain\Delivery\Zip\ZipArchiveGenerator;
use AperturePro\Domain\Delivery\Zip\ZipResult;
use AperturePro\Domain\Logs\Logger;

class DeliveryService
{
    public static function getZipGenerator(): ZipGeneratorInterface
    {
        return new ZipArchiveGenerator();
    }

    public static function handleZipSuccess(int $project_id, ZipResult $result): void
    {
        update_post_meta($project_id, 'ap_delivery_zip_url', $result->path);
        update_post_meta($project_id, 'ap_delivery_zip_size', $result->size);
        update_post_meta($project_id, 'ap_delivery_zip_date', current_time('mysql'));

        Logger::info('ZIP generation successful', [
            'url'  => $result->path,
            'size' => $result->size,
        ], $project_id);

        // Notify client? Maybe not automatically, usually admin sends link manually.
    }
}
