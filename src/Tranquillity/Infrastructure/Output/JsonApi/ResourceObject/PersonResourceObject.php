<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Model\Person\Person;

class PersonResourceObject extends AbstractResourceObject
{
    protected function generateAttributes($entity, array $fields = []): array
    {
        // Make sure entity supplied is a Person
        if (!($entity instanceof Person)) {
            throw new \InvalidArgumentException('Only ' . Person::class . ' domain entities can be used to populate this resource object');
        }

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

    protected function generateRelationships($entity, array $relatedResources = []): array
    {
        return [];
    }

    protected function generateLinks($entity): array
    {
        $links = [];

        // Generate 'self' link
        $links['self'] = $this->writeUrlForRoute('person-detail', ['id' => $entity->getIdValue()]);
        return $links;
    }
}
