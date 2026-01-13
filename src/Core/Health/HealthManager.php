<?php

namespace AperturePro\Core\Health;

class HealthManager
{
    /**
     * Run all health checks and return results.
     *
     * @return HealthCheckInterface[]
     */
    public static function getResults(): array
    {
        $checks = [
            new SchemaHealthCheck(),
            new EnvironmentHealthCheck(),
            new JobsHealthCheck(),
            new EmailHealthCheck(),
            new StorageHealthCheck(),
            new CronHealthCheck(),
            new QueueHealthCheck(),
            new TokenHealthCheck(),
            new DiskSpaceHealthCheck(),
        ];

        return $checks;
    }
}
