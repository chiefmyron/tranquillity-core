<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Enum;

final class ErrorCodeEnum
{
    // Generic error codes
    public const FIELD_VALIDATION_EMAIL_FORMAT = '10001';

    // Entity-specific error codes
    public const PERSON_DOES_NOT_EXIST = '20001';

    // Map application error codes to their equivalent HTTP status codes
    public static function statusCode(string $errorCode): int
    {
        switch ($errorCode) {
            case static::FIELD_VALIDATION_EMAIL_FORMAT:
                return HttpStatusCodeEnum::UNPROCESSABLE_ENTITY;
            case static::PERSON_DOES_NOT_EXIST:
                return HttpStatusCodeEnum::NOT_FOUND;
            default:
                return HttpStatusCodeEnum::INTERNAL_SERVER_ERROR;
        };
    }

    public static function title(string $errorCode): string
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $constants = array_flip($reflectionClass->getConstants());
        return $constants[$errorCode] ?? 'UNKNOWN_ERROR_CODE';
    }
}
