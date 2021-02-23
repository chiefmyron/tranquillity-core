<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;

class StoredEvent implements DomainEvent
{
    private int $eventId;
    private string $typeName;
    private DateTimeImmutable $occurredOn;
    private string $eventBody;

    public function __construct(
        string $typeName,
        DateTimeImmutable $occurredOn,
        string $eventBody
    ) {
        $this->typeName = $typeName;
        $this->occurredOn = $occurredOn;
        $this->eventBody = $eventBody;
    }

    public function eventId(): int
    {
        return $this->eventId;
    }

    public function typeName(): string
    {
        return $this->typeName;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public function eventBody(): string
    {
        return $this->eventBody;
    }
}
