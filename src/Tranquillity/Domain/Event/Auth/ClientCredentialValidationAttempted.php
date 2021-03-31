<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;

class ClientCredentialValidationAttempted implements DomainEvent
{
    private string $name;
    private DateTimeImmutable $occurredOn;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
