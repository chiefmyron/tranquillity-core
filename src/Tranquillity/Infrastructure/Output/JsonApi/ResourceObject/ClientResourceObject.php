<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Domain\Model\Auth\Client;

class ClientResourceObject extends AbstractResourceObject
{
    protected function generateAttributes($entity, array $fields = []): array
    {
        // Make sure entity supplied is a Client
        if (!($entity instanceof Client)) {
            throw new \InvalidArgumentException('Only ' . Client::class . ' domain entities can be used to populate this resource object');
        }

        // Build full set of attributes
        $attribs = [
            'name' => $entity->name(),
            'secret' => $entity->secret(),
            'redirectUri' => $entity->redirectUri()
        ];

        // Check if a sparse fieldset has been requested for this resource
        $sparseFieldset = $fields[EntityTypeEnum::OAUTH_CLIENT] ?? [];
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
        //$links['self'] = $this->writeUrlForRoute('user-detail', ['id' => $entity->getIdValue()]);
        return $links;
    }
}
