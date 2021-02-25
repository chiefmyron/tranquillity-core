<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person;

use Tranquillity\Application\DataTransformer\PersonDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\ResourceObjectDataTransformer;

class JsonApiPersonDataTransformer extends ResourceObjectDataTransformer implements PersonDataTransformer
{
    protected function writeAttributes($entity, array $fields = []): array
    {
        return [
            'firstName' => $entity->firstName(),
            'lastName' => $entity->lastName(),
            'jobTitle' => $entity->jobTitle(),
            'emailAddress' => $entity->emailAddress()
        ];
    }

    protected function writeRelationships($entity, array $fields = []): array
    {
        return [];
    }

    protected function writeLinks($entity): array
    {
        return [
            'self' => (string)$this->request->getUri()
        ];
    }

    protected function writeIncluded($entity, array $relatedResources = [], array $included = []): array
    {
        return [];
    }
}
