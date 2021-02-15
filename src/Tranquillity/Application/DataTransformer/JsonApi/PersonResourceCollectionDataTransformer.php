<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\JsonApi;

use Tranquillity\Application\DataTransformer\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\Person;

class PersonResourceCollectionDataTransformer extends AbstractResourceCollectionDataTransformer implements PersonCollectionDataTransformer
{
    /**
     * @param array<Person> $personCollection
     * @param array If provided, applies sparse fieldset rules
     * @param array If provided, determines which related resources are included in a compound document
     * @return void
     */
    public function write(array $personCollection, array $fields = [], array $relatedResources = [])
    {
        // Create a data transformer for the entity
        $dataTransformer = new PersonResourceObjectDataTransformer($this->request);

        foreach ($personCollection as $person) {
            $dataTransformer->write($person, $fields, $relatedResources);
            $personResource = $dataTransformer->read();
            $this->data[] = $personResource['data'];
        }
    }
}
