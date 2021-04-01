<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ListPeople;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Person\PersonCollection;

interface ListPeopleDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param PersonCollection $collection
     * @return void
     */
    public function write(PersonCollection $collection);

    /**
     * @return mixed
     */
    public function read();
}
