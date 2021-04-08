<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Auth\RefreshTokenId;

class RefreshTokenCreated implements DomainEvent
{
    private RefreshTokenId $refreshTokenId;
    private DateTimeImmutable $occurredOn;

    public function __construct(RefreshTokenId $refreshTokenId)
    {
        $this->refreshTokenId = $refreshTokenId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function refreshTokenId(): RefreshTokenId
    {
        return $this->refreshTokenId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
