<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Enum;

final class ErrorCodeEnum
{
    // Generic error codes
    public const FIELD_VALIDATION_MANDATORY_VALUE_MISSING = '10000';
    public const FIELD_VALIDATION_EMAIL_FORMAT = '10001';
    public const FIELD_VALIDATION_TIMEZONE_INVALID = '10002';
    public const FIELD_VALIDATION_LOCALE_INVALID = '10003';
    public const FIELD_VALIDATION_PASSWORD_INVALID = '10004';

    // Entity-specific error codes
    public const PERSON_DOES_NOT_EXIST = '20001';
    public const USER_DOES_NOT_EXIST = '20101';
    public const USER_ALREADY_EXISTS = '20102';

    // Map application error codes to their equivalent HTTP status codes
    public static function statusCode(string $errorCode): int
    {
        switch ($errorCode) {
            case static::FIELD_VALIDATION_PASSWORD_INVALID:
                return HttpStatusCodeEnum::BAD_REQUEST;
            case static::FIELD_VALIDATION_MANDATORY_VALUE_MISSING:
            case static::FIELD_VALIDATION_EMAIL_FORMAT:
            case static::FIELD_VALIDATION_TIMEZONE_INVALID:
            case static::FIELD_VALIDATION_LOCALE_INVALID:
            case static::USER_ALREADY_EXISTS:
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
