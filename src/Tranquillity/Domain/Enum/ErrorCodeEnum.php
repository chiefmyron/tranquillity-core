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

    public const OAUTH_CLIENT_DOES_NOT_EXIST = '20201';
    public const OAUTH_ACCESS_TOKEN_DOES_NOT_EXIST = '20202';
    public const OAUTH_ACCESS_TOKEN_ALREADY_EXISTS = '20203';
    public const OAUTH_REFRESH_TOKEN_DOES_NOT_EXIST = '20204';
    public const OAUTH_REFRESH_TOKEN_ALREADY_EXISTS = '20205';
    public const OAUTH_AUTHORIZATION_CODE_DOES_NOT_EXIST = '20206';
    public const OAUTH_AUTHORIZATION_CODE_ALREADY_EXISTS = '20207';

    public static function title(string $errorCode): string
    {
        $reflectionClass = new \ReflectionClass(self::class);
        $constants = array_flip($reflectionClass->getConstants());
        return $constants[$errorCode] ?? 'UNKNOWN_ERROR_CODE';
    }
}
