<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ViewPerson;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Person\Person;

interface ViewPersonDataTransformer extends ApplicationDataTransformer
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
