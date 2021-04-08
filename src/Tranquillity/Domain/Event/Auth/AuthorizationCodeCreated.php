<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeId;

class AuthorizationCodeCreated implements DomainEvent
{
    private AuthorizationCodeId $authorizationCodeId;
    private DateTimeImmutable $occurredOn;

    public function __construct(AuthorizationCodeId $authorizationCodeId)
    {
        $this->authorizationCodeId = $authorizationCodeId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function authorizationCodeId(): AuthorizationCodeId
    {
        return $this->authorizationCodeId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
