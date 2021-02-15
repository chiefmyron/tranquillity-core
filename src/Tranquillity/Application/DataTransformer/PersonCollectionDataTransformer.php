<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer;

use Tranquillity\Domain\Model\Person\Person;

interface PersonCollectionDataTransformer
{
    /**
     * @param array<Person> $personCollection
     * @return void
     */
    public function write(array $personCollection);

    /**
     * @return mixed
     */
    public function read();
}
