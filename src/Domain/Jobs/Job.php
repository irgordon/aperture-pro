<?php

namespace AperturePro\Domain\Jobs;

class Job
{
    public int $id;
    public int $project_id;
    public string $type;
    public string $status;
    public int $attempts;
    public int $max_attempts;
    public ?string $last_error;
    public ?string $payload;
    public string $created_at;
    public string $updated_at;

    public function __construct(object $row)
    {
        $this->id           = (int) $row->id;
        $this->project_id   = (int) $row->project_id;
        $this->type         = (string) $row->type;
        $this->status       = (string) $row->status;
        $this->attempts     = (int) $row->attempts;
        $this->max_attempts = (int) $row->max_attempts;
        $this->last_error   = $row->last_error;
        $this->payload      = $row->payload;
        $this->created_at   = (string) $row->created_at;
        $this->updated_at   = (string) $row->updated_at;
    }

    public function payloadArray(): array
    {
        if (!$this->payload) {
            return [];
        }

        $decoded = json_decode($this->payload, true);
        return is_array($decoded) ? $decoded : [];
    }
}
