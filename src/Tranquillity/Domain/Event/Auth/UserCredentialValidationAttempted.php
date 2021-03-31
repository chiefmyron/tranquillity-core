<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;

class UserCredentialValidationAttempted implements DomainEvent
{
    private string $username;
    private DateTimeImmutable $occurredOn;

    public function __construct(string $username)
    {
        $this->username = $username;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function username(): string
    {
        return $this->username;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
