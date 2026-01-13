<?php

namespace AperturePro\Domain\Delivery\Zip;

use ZipArchive;
use AperturePro\Storage\StorageManager;
use AperturePro\Support\Error;

class ZipArchiveGenerator implements ZipGeneratorInterface
{
    public function generate(int $project_id): ZipResult
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'ap_zip_');
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Error::logAndThrow('Could not create temporary zip file', ['project_id' => $project_id]);
        }

        $addedNames = [];
        $batch_size = 100; // Process 100 images at a time
        $offset = 0;

        while (true) {
            $images = ap_get_project_images($project_id, $batch_size, $offset);

            if (empty($images)) {
                break; // No more images
            }

            foreach ($images as $image) {
                $filePath = get_attached_file($image->ID);
                if ($filePath && file_exists($filePath)) {
                    $fileName = basename($filePath);

                    // Handle duplicate filenames
                    $base = pathinfo($fileName, PATHINFO_FILENAME);
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $count = 1;
                    while (isset($addedNames[$fileName])) {
                        $fileName = $base . '-' . $count . '.' . $ext;
                        $count++;
                    }
                    $addedNames[$fileName] = true;

                    $zip->addFile($filePath, $fileName);
                }
            }
            $offset += $batch_size;
        }

        $zip->close();

        // Calculate size
        $size = filesize($tempFile);

        // Move to storage
        $targetPath = 'projects/' . $project_id . '/delivery_' . date('Ymd_His') . '.zip';
        $publicUrl = StorageManager::adapter()->store($tempFile, $targetPath);

        // Cleanup temp file
        unlink($tempFile);

        return new ZipResult($publicUrl, $size, false);
    }
}
