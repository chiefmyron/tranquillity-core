<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAuthorizationCode;

use DateTimeImmutable;

class CreateAuthorizationCodeRequest
{
    private string $code;
    private string $clientName;
    private ?string $username;
    private DateTimeImmutable $expires;
    private string $redirectUri;
    private array $scopes;

    public function __construct(
        string $code,
        string $clientName,
        ?string $username,
        DateTimeImmutable $expires,
        string $redirectUri,
        array $scopes
    ) {
        $this->code = $code;
        $this->clientName = $clientName;
        $this->username = $username;
        $this->expires = $expires;
        $this->redirectUri = $redirectUri;
        $this->scopes = $scopes;
    }

    public function code(): string
    {
        return $this->code;
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

    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    public function scopes(): array
    {
        return $this->scopes;
    }
}
