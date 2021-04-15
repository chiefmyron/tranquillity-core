<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateRefreshToken;

use DateTimeImmutable;

class CreateRefreshTokenRequest
{
    private string $token;
    private string $clientName;
    private ?string $username;
    private DateTimeImmutable $expires;
    private array $scopes;

    public function __construct(
        string $token,
        string $clientName,
        ?string $username,
        DateTimeImmutable $expires,
        array $scopes
    ) {
        $this->token = $token;
        $this->clientName = $clientName;
        $this->username = $username;
        $this->expires = $expires;
        $this->scopes = $scopes;
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

    public function expires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function scopes(): array
    {
        return $this->scopes;
    }
}
