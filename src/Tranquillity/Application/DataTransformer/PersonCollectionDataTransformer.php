<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer;

use Tranquillity\Domain\Model\Person\PersonCollection;

interface PersonCollectionDataTransformer
{
    /**
     * @param PersonCollection $personCollection
     * @return void
     */
    public function write(PersonCollection $personCollection);

    /**
     * @return mixed
     */
    public function read();
}
