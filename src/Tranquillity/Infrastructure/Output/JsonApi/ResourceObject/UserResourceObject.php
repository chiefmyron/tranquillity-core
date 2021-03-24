<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Model\User\User;

class UserResourceObject extends AbstractResourceObject
{
    protected function generateAttributes($entity, array $fields = []): array
    {
        // Make sure entity supplied is a User
        if (!($entity instanceof User)) {
            throw new \InvalidArgumentException('Only ' . User::class . ' domain entities can be used to populate this resource object');
        }

        // Build full set of attributes
        $attribs = [
            'username' => $entity->username(),
            'timezoneCode' => $entity->timezoneCode(),
            'localeCode' => $entity->localeCode(),
            'active' => $entity->active(),
            'registeredDateTime' => $entity->registeredDateTime()
        ];

        // Check if a sparse fieldset has been requested for this resource
        $sparseFieldset = $fields[EntityTypeEnum::USER] ?? [];
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
        $links['self'] = $this->urlForRoute('user-detail', ['id' => $entity->getIdValue()]);
        return $links;
    }
}
