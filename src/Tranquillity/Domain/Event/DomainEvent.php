<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredOn(): DateTimeImmutable;
}
