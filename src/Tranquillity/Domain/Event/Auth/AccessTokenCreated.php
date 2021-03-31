<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Auth\AccessTokenId;

class AccessTokenCreated implements DomainEvent
{
    private AccessTokenId $accessTokenId;
    private DateTimeImmutable $occurredOn;

    public function __construct(AccessTokenId $accessTokenId)
    {
        $this->accessTokenId = $accessTokenId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function accessTokenId(): AccessTokenId
    {
        return $this->accessTokenId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
