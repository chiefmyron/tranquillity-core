<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event;

interface DomainEventStore
{
    public function append(DomainEvent $domainEvent): void;

    public function allStoredEventsSince($eventId): array;
}
