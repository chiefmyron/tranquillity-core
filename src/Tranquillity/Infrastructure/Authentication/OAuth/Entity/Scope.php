<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope implements ScopeEntityInterface
{
    private string $identifier;

    public function __construct(string $id)
    {
        $this->identifier = $id;
    }
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
