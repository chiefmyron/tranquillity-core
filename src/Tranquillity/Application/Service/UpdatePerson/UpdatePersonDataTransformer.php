<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\UpdatePerson;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Person\Person;

interface UpdatePersonDataTransformer extends ApplicationDataTransformer
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
