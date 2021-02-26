<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Domain\Model\DomainEntity;

abstract class ResourceObjectDataTransformer
{
    protected ServerRequestInterface $request;

    protected array $data = [];
    protected array $included = [];
    protected array $links = [];
    protected array $meta = [];

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
        $relationships = $this->writeRelationships($entity, $fields);
        if (count($relationships) > 0) {
            $this->data['relationships'] = $relationships;
        }

        // Add links
        $this->links = $this->writeLinks($entity);

        // Add includes
        $this->included = $this->writeIncluded($entity, $relatedResources, $this->included);
    }

    public function read(): array
    {
        // Build return array
        $result = [];
        if (count($this->links) > 0) {
            $result['links'] = $this->links;
        }
        if (count($this->data) > 0) {
            $result['data'] = $this->data;
        }
        if (count($this->included) > 0) {
            $result['included'] = $this->included;
        }
        if (count($this->meta) > 0) {
            $result['meta'] = $this->meta;
        }
        $result['jsonapi'] = ['version' => '1.0'];
        return $result;
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

    protected function applySparseFieldset(array $attributes, array $fields): array
    {
        if (count($fields) <= 0) {
            // No sparse fieldset requested - return full resource
            return $attributes;
        }

        // If a sparse fieldset has been defined for this entity type, apply it now
        $sparseAttribs = [];
        foreach ($fields as $field) {
            if (isset($attributes[$field]) == true) {
                $sparseAttribs[$field] = $attributes[$field];
            }
        }
        return $sparseAttribs;
    }
}
