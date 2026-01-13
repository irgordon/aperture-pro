<?php

namespace AperturePro\Domain\Jobs;

use AperturePro\Domain\Logs\Logger;
use AperturePro\Domain\Delivery\DeliveryService;
use AperturePro\Domain\Delivery\Zip\ZipResult;
use AperturePro\Domain\ImageOptimization\ImageOptimizationService;
use AperturePro\Domain\ImageOptimization\OptimizationResult;

class JobRunner
{
    public static function handle(int $job_id): void
    {
        $job = JobRepository::find($job_id);
        if (!$job) {
            return;
        }

        JobRepository::updateStatus($job, JobState::RUNNING);

        try {
            switch ($job->type) {
                case JobTypes::ZIP_GENERATION:
                    self::runZipJob($job);
                    break;

                case JobTypes::IMAGE_OPTIMIZATION:
                    self::runImageOptimizationJob($job);
                    break;

                default:
                    throw new \RuntimeException('Unknown job type: ' . $job->type);
            }

            JobRepository::updateStatus($job, JobState::SUCCEEDED);

            Logger::info('Job succeeded', [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'type'       => $job->type,
            ], $job->project_id);

            do_action(JobEvents::SUCCEEDED, [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'type'       => $job->type,
            ]);

        } catch (\Throwable $e) {
            JobRepository::updateStatus($job, JobState::FAILED, $e->getMessage());

            Logger::error('Job failed', [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'type'       => $job->type,
                'error'      => $e->getMessage(),
            ], $job->project_id);

            do_action(JobEvents::FAILED, [
                'job_id'     => $job->id,
                'project_id' => $job->project_id,
                'type'       => $job->type,
                'error'      => $e->getMessage(),
            ]);

            JobScheduler::scheduleRetry($job);
        }
    }

    protected static function runZipJob(Job $job): void
    {
        $project_id = $job->project_id;

        $generator = DeliveryService::getZipGenerator(); // returns ZipGeneratorInterface
        /** @var ZipResult $result */
        $result = $generator->generate($project_id);

        DeliveryService::handleZipSuccess($project_id, $result);
    }

    protected static function runImageOptimizationJob(Job $job): void
    {
        $project_id = $job->project_id;

        $optimizer = ImageOptimizationService::getOptimizer();
        /** @var OptimizationResult $result */
        $result = $optimizer->optimize($project_id);

        ImageOptimizationService::handleSuccess($project_id, $result);
    }
}
