<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer;

use Tranquillity\Domain\Model\Person\Person;

class PersonArrayDataTransformer implements PersonDataTransformer
{
    private array $data = [];

    public function write(Person $person)
    {
        $this->data = [
            'id' => $person->getIdValue(),
            'firstName' => $person->firstName(),
            'lastName' => $person->lastName(),
            'jobTitle' => $person->jobTitle(),
            'emailAddress' => $person->emailAddress()
        ];
    }

    public function read(): array
    {
        return $this->data;
    }
}
