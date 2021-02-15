<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer;

use Tranquillity\Domain\Model\Person\Person;

interface PersonDataTransformer
{
    /**
     * @param Person $person
     * @return void
     */
    public function write(Person $person);

    /**
     * @return mixed
     */
    public function read();
}
