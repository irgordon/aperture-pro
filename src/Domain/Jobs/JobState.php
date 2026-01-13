<?php

namespace AperturePro\Domain\Jobs;

class JobState
{
    public const QUEUED      = 'queued';
    public const RUNNING     = 'running';
    public const FAILED      = 'failed';
    public const RETRYING    = 'retrying';
    public const SUCCEEDED   = 'succeeded';
    public const DEAD_LETTER = 'dead_letter';

    public static function all(): array
    {
        return [
            self::QUEUED,
            self::RUNNING,
            self::FAILED,
            self::RETRYING,
            self::SUCCEEDED,
            self::DEAD_LETTER,
        ];
    }
}
