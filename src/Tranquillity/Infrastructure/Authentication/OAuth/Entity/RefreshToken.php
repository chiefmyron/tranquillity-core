<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Entity;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class RefreshToken implements RefreshTokenEntityInterface
{
    // Use package traits for convenience
    use RefreshTokenTrait;
    use EntityTrait;
}
