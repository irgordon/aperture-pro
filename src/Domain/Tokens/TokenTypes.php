<?php

namespace AperturePro\Domain\Tokens;

class TokenTypes
{
    public const ACCESS   = 'access';   // client portal access
    public const DOWNLOAD = 'download'; // final ZIP download

    public static function all(): array
    {
        return [
            self::ACCESS,
            self::DOWNLOAD,
        ];
    }
}
