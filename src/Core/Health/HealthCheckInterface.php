<?php

namespace AperturePro\Core\Health;

interface HealthCheckInterface
{
    /**
     * Get the title of the health check.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get the status of the health check.
     *
     * @return string 'ok', 'warning', 'error', 'info'
     */
    public function getStatus(): string;

    /**
     * Get a user-friendly message.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Get technical details (array or string).
     *
     * @return mixed
     */
    public function getDetails();
}
