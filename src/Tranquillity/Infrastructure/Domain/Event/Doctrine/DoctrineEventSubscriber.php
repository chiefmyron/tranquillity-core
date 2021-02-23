<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Event\Doctrine;

use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Event\DomainEventSubscriber;

class DoctrineEventSubscriber implements DomainEventSubscriber
{
    private DoctrineEventStore $eventStore;

    public function __construct(DoctrineEventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function handle(DomainEvent $event): void
    {
        $this->eventStore->append($event);
    }

    public function isSubscribedTo(DomainEvent $event): bool
    {
        return true;
    }
}
