<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Enum;

final class EntityTypeEnum
{
    public const PERSON = 'person';
    public const USER = 'user';

    public const OAUTH_CLIENT = 'oauth_client';
    public const OAUTH_TOKEN_ACCESS = 'oauth_token_access';
}
