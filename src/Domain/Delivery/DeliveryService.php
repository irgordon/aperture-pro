<?php

namespace AperturePro\Domain\Delivery;

use AperturePro\Domain\Delivery\Zip\ZipGeneratorInterface;
use AperturePro\Domain\Delivery\Zip\ZipArchiveGenerator;
use AperturePro\Domain\Delivery\Zip\ZipResult;
use AperturePro\Domain\Logs\Logger;
use AperturePro\Domain\Notification\NotificationService;

class DeliveryService
{
    public static function getZipGenerator(): ZipGeneratorInterface
    {
        return new ZipArchiveGenerator();
    }

    public static function handleZipSuccess(int $project_id, ZipResult $result): void
    {
        DeliveryRepository::save($project_id, $result->path, $result->size);

        Logger::info('ZIP generation successful', [
            'url'  => $result->path,
            'size' => $result->size,
        ], $project_id);

        // Notify client automatically
        $notificationService = new NotificationService();
        $notificationService->sendClientZipReadyEmail($project_id, $result->path);
    }
}
