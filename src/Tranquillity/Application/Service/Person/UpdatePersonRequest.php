<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

class UpdatePersonRequest
{
    private string $id;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $jobTitle;
    private ?string $emailAddress;

    private array $updatedAttributes = [];
    private array $fields = [];
    private array $relatedResources = [];

    public function __construct(
        string $id,
        array $fields,
        array $relatedResources,
        ?string $firstName,
        ?string $lastName,
        ?string $jobTitle,
        ?string $emailAddress
    ) {
        $this->id = $id;
        $this->fields = $fields;
        $this->relatedResources = $relatedResources;

        $this->firstName = $firstName;
        if (is_null($firstName) == false) {
            $this->updatedAttributes[] = 'firstName';
        }
        $this->lastName = $lastName;
        if (is_null($lastName) == false) {
            $this->updatedAttributes[] = 'lastName';
        }
        $this->jobTitle = $jobTitle;
        if (is_null($jobTitle) == false) {
            $this->updatedAttributes[] = 'jobTitle';
        }
        $this->emailAddress = $emailAddress;
        if (is_null($emailAddress) == false) {
            $this->updatedAttributes[] = 'emailAddress';
        }
    }

    public static function createFromArray(string $id, array $fields, array $relatedResources, array $attributes): self
    {
        return new self(
            $id,
            $fields,
            $relatedResources,
            $attributes['firstName'] ?? null,
            $attributes['lastName'] ?? null,
            $attributes['jobTitle'] ?? null,
            $attributes['emailAddress'] ?? null
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

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function jobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function emailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function getUpdatedAttributes(): array
    {
        return $this->updatedAttributes;
    }
}
