<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\JsonApi;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Domain\Model\DomainEntity;

abstract class AbstractResourceObjectDataTransformer
{
    protected ServerRequestInterface $request;

    protected array $data = [];
    protected array $included = [];
    protected array $links = [];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function write(DomainEntity $entity, array $fields = [], array $relatedResources = [])
    {
        // Write common identification block for entity
        $this->data = $this->writeIdentification($entity);

        // Add attributes
        $this->data['attributes'] = $this->writeAttributes($entity, $fields);

        // Add relationships
        $this->data['relationships'] = $this->writeRelationships($entity, $fields);

        // Add links
        $this->links = $this->writeLinks($entity);

        // Add includes
        $this->included = $this->writeIncluded($entity, $relatedResources, $this->included);
    }

    public function read(): array
    {
        return [
            'links' => $this->links,
            'data' => $this->data,
            'included' => $this->included
        ];
    }

    protected function writeIdentification(DomainEntity $entity): array
    {
        return [
            'type' => $entity->getEntityType(),
            'id' => $entity->getIdValue()
        ];
    }

    abstract protected function writeAttributes($entity, array $fields = []): array;

    abstract protected function writeRelationships($entity, array $fields = []): array;

    abstract protected function writeLinks($entity): array;

    abstract protected function writeIncluded($entity, array $relatedResources = [], array $included = []): array;
}
