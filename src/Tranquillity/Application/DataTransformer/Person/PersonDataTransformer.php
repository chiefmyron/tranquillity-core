<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\Person;

use Tranquillity\Domain\Model\Person\Person;

interface PersonDataTransformer
{
    /**
     * @param Person $entity
     * @return void
     */
    public function write(Person $entity);

    /**
     * @return mixed
     */
    public function read();
}
