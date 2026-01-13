<?php

namespace AperturePro\Domain\Jobs;

use AperturePro\Domain\Logs\Logger;
use AperturePro\Domain\Delivery\DeliveryService;
use AperturePro\Domain\Delivery\Zip\ZipResult;
use AperturePro\Domain\ImageOptimization\ImageOptimizationService;
use AperturePro\Domain\ImageOptimization\OptimizationResult;
use AperturePro\Domain\Notification\NotificationService;

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
            $finished = true;
            switch ($job->type) {
                case JobTypes::ZIP_GENERATION:
                    self::runZipJob($job);
                    break;

                case JobTypes::IMAGE_OPTIMIZATION:
                    $finished = self::runImageOptimizationJob($job);
                    break;

                case JobTypes::EMAIL_BATCH:
                    $finished = self::runEmailBatchJob($job);
                    break;

                default:
                    throw new \RuntimeException('Unknown job type: ' . $job->type);
            }

            if ($finished === false) {
                Logger::info('Job continuing', [
                    'job_id'     => $job->id,
                    'project_id' => $job->project_id,
                    'type'       => $job->type,
                ], $job->project_id);

                // Reschedule immediately for continuation
                wp_schedule_single_event(time(), 'ap_run_job', [$job->id]);
            } else {
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
            }

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

    protected static function runImageOptimizationJob(Job $job): bool
    {
        $project_id = $job->project_id;

        $optimizer = ImageOptimizationService::getOptimizer();
        /** @var OptimizationResult $result */
        $result = $optimizer->optimize($project_id);

        ImageOptimizationService::handleSuccess($project_id, $result);

        return $result->completed;
    }

    protected static function runEmailBatchJob(Job $job): bool
    {
        $payload = $job->payloadArray();
        $recipients = $payload['recipients'] ?? [];
        $subject = $payload['subject'] ?? '';
        $message = $payload['message'] ?? '';

        if (empty($recipients) || empty($subject) || empty($message)) {
            throw new \RuntimeException('Invalid payload for email batch job.');
        }

        // Process in batches of 20 to avoid timeouts
        $batchSize = 20;
        $currentBatch = array_slice($recipients, 0, $batchSize);
        $remaining = array_slice($recipients, $batchSize);

        $service = new NotificationService();
        $failed = $service->sendBatch($currentBatch, $subject, $message);

        if (!empty($failed)) {
            Logger::warning('Some emails failed to send in batch job ' . $job->id, [
                'failed_recipients' => $failed,
            ], $job->project_id);

            // If the entire current batch failed, we might want to throw an exception
            // to trigger a retry. If it's a mix, we proceed.
            if (count($failed) === count($currentBatch)) {
                throw new \RuntimeException('All emails in current batch failed to send.');
            }
        }

        if (!empty($remaining)) {
            $payload['recipients'] = $remaining;
            JobRepository::updatePayload($job, $payload);
            return false; // Not finished
        }

        return true; // Finished
    }
}
