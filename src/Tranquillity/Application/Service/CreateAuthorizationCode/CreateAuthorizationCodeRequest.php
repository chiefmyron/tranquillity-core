<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAuthorizationCode;

use DateTime;

class CreateAuthorizationCodeRequest
{
    private string $code;
    private string $clientName;
    private ?string $username;
    private DateTime $expires;
    private string $redirectUri;
    private ?string $scope;

    public function __construct(
        string $code,
        string $clientName,
        ?string $username,
        DateTime $expires,
        string $redirectUri,
        ?string $scope
    ) {
        $this->code = $code;
        $this->clientName = $clientName;
        $this->username = $username;
        $this->expires = $expires;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
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

    public function expires(): DateTime
    {
        return $this->expires;
    }

    public function redirectUri(): string
    {
        return $this->redirectUri;
    }

    public function scope(): ?string
    {
        return $this->scope;
    }
}
