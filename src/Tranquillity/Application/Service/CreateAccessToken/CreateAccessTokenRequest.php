<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAccessToken;

use DateTimeImmutable;

class CreateAccessTokenRequest
{
    private string $token;
    private string $clientId;
    private ?string $username;
    private DateTimeImmutable $expires;
    private array $scopes;

    public function __construct(
        string $token,
        string $clientId,
        ?string $username,
        DateTimeImmutable $expires,
        array $scopes
    ) {
        $this->token = $token;
        $this->clientId = $clientId;
        $this->username = $username;
        $this->expires = $expires;
        $this->scopes = $scopes;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function clientId(): string
    {
        return $this->clientId;
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
