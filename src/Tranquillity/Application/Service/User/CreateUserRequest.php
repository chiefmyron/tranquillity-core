<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\User;

class CreateUserRequest
{
    private string $username;
    private string $password;
    private string $timezoneCode;
    private string $localeCode;
    private bool $active;

    public function __construct(
        string $username,
        string $password,
        string $timezoneCode,
        string $localeCode,
        bool $active
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->timezoneCode = $timezoneCode;
        $this->localeCode = $localeCode;
        $this->active = $active;
    }

    public static function createFromArray(array $attributes): self
    {
        return new self(
            $attributes['username'] ?? '',
            $attributes['password'] ?? '',
            $attributes['timezoneCode'] ?? '',
            $attributes['localeCode'] ?? '',
            $attributes['active'] ?? true
        );
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function timezoneCode(): string
    {
        return $this->timezoneCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
