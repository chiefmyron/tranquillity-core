<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

class CreatePersonRequest
{
    private string $firstName;
    private string $lastName;
    private string $jobTitle;
    private string $emailAddress;

    public function __construct(
        string $firstName,
        string $lastName,
        string $jobTitle,
        string $emailAddress
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->jobTitle = $jobTitle;
        $this->emailAddress = $emailAddress;
    }

    public static function createFromArray(array $attributes): self
    {
        return new self(
            $attributes['firstName'] ?? '',
            $attributes['lastName'] ?? '',
            $attributes['jobTitle'] ?? '',
            $attributes['emailAddress'] ?? ''
        );
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
