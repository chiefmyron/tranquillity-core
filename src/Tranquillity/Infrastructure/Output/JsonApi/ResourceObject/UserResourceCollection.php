<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

class UserResourceCollection extends AbstractResourceCollection
{
    protected function generateCollection($entityCollection, array $fields = [], array $relatedResources = []): array
    {
        $entities = $entityCollection->collection();

        // Create resource object that represents the person
        $resourceObjects = [];
        foreach ($entities as $person) {
            $resourceObject = new UserResourceObject($this->routeCollector);
            $resourceObject->populate($person, $fields, $relatedResources);
            $resourceObjects[] = $resourceObject;
        }
        return $resourceObjects;
    }
}
