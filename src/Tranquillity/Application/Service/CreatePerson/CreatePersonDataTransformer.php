<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreatePerson;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Person\Person;

interface CreatePersonDataTransformer extends ApplicationDataTransformer
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
