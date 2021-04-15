<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Entity;

use League\OAuth2\Server\Entities\UserEntityInterface;

class User implements UserEntityInterface
{
    private string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function getIdentifier()
    {
        return $this->username;
    }
}
