<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Event\Doctrine;

use Doctrine\ORM\EntityRepository;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Domain\Event\StoredEvent;
use Zumba\JsonSerializer\JsonSerializer;

class DoctrineEventStore extends EntityRepository implements DomainEventStore
{
    private ?JsonSerializer $serializer = null;

    public function append(DomainEvent $event): void
    {
        $storedEvent = new StoredEvent(
            get_class($event),
            $event->occurredOn(),
            $this->serializer()->serialize($event)
        );

        $this->getEntityManager()->persist($storedEvent);
    }

    public function allStoredEventsSince($eventId): array
    {
        $query = $this->createQueryBuilder('e');
        if ($eventId) {
            $query->where('e.eventId > :eventId');
            $query->setParameters(['eventId' => $eventId]);
        }
        $query->orderBy('e.eventId');
        return $query->getQuery()->getResult();
    }

    private function serializer(): JsonSerializer
    {
        if (null === $this->serializer) {
            $this->serializer = new JsonSerializer();
        }
        return $this->serializer;
    }
}
