<?php

namespace AperturePro\Domain\Proofing;

class ProofingState
{
    public const OPEN      = 'open';
    public const SUBMITTED = 'submitted';
    public const REOPENED  = 'reopened';

    public static function all(): array
    {
        return [
            self::OPEN,
            self::SUBMITTED,
            self::REOPENED,
        ];
    }
}
