<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindUserByUsername;

class FindUserByUsernameRequest
{
    private string $username;

    public function __construct(string $username)
    {
        $this->username = $username;
    }

    public function username(): string
    {
        return $this->username;
    }
}
