<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\UpdateUser;

class UpdateUserRequest
{
    private string $id;
    private ?string $username;
    private ?string $password;
    private ?string $timezoneCode;
    private ?string $localeCode;
    private ?bool $active;

    private array $fields = [];
    private array $relatedResources = [];

    private array $updatedAttributes = [];

    public function __construct(
        string $id,
        array $fields,
        array $relatedResources,
        ?string $username,
        ?string $password,
        ?string $timezoneCode,
        ?string $localeCode,
        ?bool $active
    ) {
        $this->id = $id;
        $this->fields = $fields;
        $this->relatedResources = $relatedResources;

        $this->username = $username;
        if (is_null($username) == false) {
            $this->updatedAttributes[] = 'username';
        }
        $this->password = $password;
        if (is_null($password) == false) {
            $this->updatedAttributes[] = 'password';
        }
        $this->timezoneCode = $timezoneCode;
        if (is_null($timezoneCode) == false) {
            $this->updatedAttributes[] = 'timezoneCode';
        }
        $this->localeCode = $localeCode;
        if (is_null($localeCode) == false) {
            $this->updatedAttributes[] = 'localeCode';
        }
        $this->active = $active;
        if (is_null($active) == false) {
            $this->updatedAttributes[] = 'active';
        }
    }

    public static function createFromArray(string $id, array $fields, array $relatedResources, array $attributes): self
    {
        return new self(
            $id,
            $fields,
            $relatedResources,
            $attributes['username'] ?? null,
            $attributes['password'] ?? null,
            $attributes['timezoneCode'] ?? null,
            $attributes['localeCode'] ?? null,
            $attributes['active'] ?? true
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function relatedResources(): array
    {
        return $this->relatedResources;
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

    public function getUpdatedAttributes(): array
    {
        return $this->updatedAttributes;
    }
}
