<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Person;

interface PersonRepository
{
    public function nextIdentity(): PersonId;

    public function add(Person $person): void;

    public function remove(Person $person): void;

    public function update(Person $person): void;

    public function findById(PersonId $id): Person;

    public function list(array $filterConditions, array $sortConditions, int $pageNumber, int $pageSize): array;
}
