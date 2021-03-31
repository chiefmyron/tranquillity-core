<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Auth\UserId;

class UserCreated implements DomainEvent
{
    private UserId $userId;
    private DateTimeImmutable $occurredOn;

    public function __construct(UserId $userId)
    {
        $this->userId = $userId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
