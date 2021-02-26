<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person;

use Tranquillity\Application\DataTransformer\PersonDataTransformer;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\ResourceObjectDataTransformer;

class JsonApiPersonDataTransformer extends ResourceObjectDataTransformer implements PersonDataTransformer
{
    protected function writeAttributes($entity, array $fields = []): array
    {
        // Build full set of attributes
        $attribs = [
            'firstName' => $entity->firstName(),
            'lastName' => $entity->lastName(),
            'jobTitle' => $entity->jobTitle(),
            'emailAddress' => $entity->emailAddress()
        ];

        // Check if a sparse fieldset has been requested for this resource
        $sparseFieldset = $fields[EntityTypeEnum::PERSON] ?? [];
        return $this->applySparseFieldset($attribs, $sparseFieldset);
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
