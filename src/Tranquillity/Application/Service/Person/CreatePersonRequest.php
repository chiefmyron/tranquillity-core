<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

class CreatePersonRequest
{
    private string $firstName;
    private string $lastName;
    private string $jobTitle;
    private string $emailAddress;

    private array $fields = [];
    private array $relatedResources = [];

    public function __construct(
        array $fields,
        array $relatedResources,
        string $firstName,
        string $lastName,
        string $jobTitle,
        string $emailAddress
    ) {
        $this->fields = $fields;
        $this->relatedResources = $relatedResources;

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->jobTitle = $jobTitle;
        $this->emailAddress = $emailAddress;
    }

    public static function createFromArray(array $fields, array $relatedResources, array $attributes): self
    {
        return new self(
            $fields,
            $relatedResources,
            $attributes['firstName'] ?? '',
            $attributes['lastName'] ?? '',
            $attributes['jobTitle'] ?? '',
            $attributes['emailAddress'] ?? ''
        );
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function relatedResources(): array
    {
        return $this->relatedResources;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function jobTitle(): string
    {
        return $this->jobTitle;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }
}
