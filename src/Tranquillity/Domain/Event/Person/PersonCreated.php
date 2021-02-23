<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Person;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Person\PersonId;

class PersonCreated implements DomainEvent
{
    private PersonId $personId;
    private DateTimeImmutable $occurredOn;

    public function __construct(PersonId $personId)
    {
        $this->personId = $personId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function personId(): PersonId
    {
        return $this->personId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
