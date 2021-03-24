<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\Person;

use Tranquillity\Domain\Model\Person\PersonCollection;

interface PersonCollectionDataTransformer
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
