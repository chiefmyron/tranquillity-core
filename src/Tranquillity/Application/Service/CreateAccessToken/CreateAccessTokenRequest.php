<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAccessToken;

use DateTime;

class CreateAccessTokenRequest
{
    private string $token;
    private string $clientName;
    private ?string $userId;
    private DateTime $expires;
    private ?string $scope;

    public function __construct(
        string $token,
        string $clientName,
        ?string $userId,
        DateTime $expires,
        ?string $scope
    ) {
        $this->token = $token;
        $this->clientName = $clientName;
        $this->userId = $userId;
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

    public function userId(): ?string
    {
        return $this->userId;
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
