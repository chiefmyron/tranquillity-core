<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateRefreshToken;

use DateTime;

class CreateRefreshTokenRequest
{
    private string $token;
    private string $clientName;
    private ?string $username;
    private DateTime $expires;
    private ?string $scope;

    public function __construct(
        string $token,
        string $clientName,
        ?string $username,
        DateTime $expires,
        ?string $scope
    ) {
        $this->token = $token;
        $this->clientName = $clientName;
        $this->username = $username;
        $this->expires = $expires;
        $this->scope = $scope;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function clientName(): string
    {
        return $this->clientName;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function expires(): DateTime
    {
        return $this->expires;
    }

    public function scope(): ?string
    {
        return $this->scope;
    }
}
