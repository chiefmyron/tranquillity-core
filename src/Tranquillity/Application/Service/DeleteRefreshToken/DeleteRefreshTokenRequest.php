<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteRefreshToken;

class DeleteRefreshTokenRequest
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function token(): string
    {
        return $this->token;
    }
}
