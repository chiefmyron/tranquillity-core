<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event\Auth;

use DateTimeImmutable;
use Tranquillity\Domain\Event\DomainEvent;
use Tranquillity\Domain\Model\Auth\ClientId;

class ClientCreated implements DomainEvent
{
    private ClientId $clientId;
    private DateTimeImmutable $occurredOn;

    public function __construct(ClientId $clientId)
    {
        $this->clientId = $clientId;
        $this->occurredOn = new DateTimeImmutable();
    }

    public function clientId(): ClientId
    {
        return $this->clientId;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
