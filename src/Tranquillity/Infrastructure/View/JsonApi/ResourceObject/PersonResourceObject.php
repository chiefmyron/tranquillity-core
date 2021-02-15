<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\View\JsonApi\ResourceObject;

use Tranquillity\Domain\Model\Person\Person;

class PersonResourceObject
{
    private Person $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function write(): array
    {
        return [
            'type' => $this->person->getEntityType(),
            'id' => $this->person->getIdValue(),
            'attributes' => [
                'firstName' => $this->person->firstName(),
                'lastName' => $this->person->lastName(),
                'jobTitle' => $this->person->jobTitle(),
                'emailAddress' => $this->person->emailAddress()
            ],
            'relationships' => [],
            'links' => [],
            'meta' => []
        ];
    }
}
