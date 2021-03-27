<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Carbon\Carbon;
use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Domain\Model\DomainEntity;

abstract class AbstractResourceObject
{
    protected string $id;
    protected string $type;
    protected array $attributes;
    protected array $relationships;
    protected array $links;
    protected array $meta;
    protected RouteCollectorInterface $routeCollector;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function populate(DomainEntity $entity, array $fields = [], array $relatedResources = []): void
    {
        $this->setId($entity->getIdValue());
        $this->setType($entity->getEntityType());
        $this->setAttributes($this->generateAttributes($entity, $fields));
        $this->setRelationships($this->generateRelationships($entity, $relatedResources));
        $this->setLinks($this->generateLinks($entity));
        return;
    }

    public function render(): array
    {
        // Resource will always include the resource identifier
        $resourceObject = $this->renderResourceIdentifier();

        // Include attributes
        if (count($this->attributes) > 0) {
            $resourceObject['attributes'] = $this->attributes;
        }

        // Include relationships
        if (count($this->relationships) > 0) {
            $resourceObject['relationships'] = $this->relationships;
        }

        // Include links
        if (count($this->links) > 0) {
            $resourceObject['links'] = $this->links;
        }

        return $resourceObject;
    }

    public function renderResourceIdentifier(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type
        ];
    }

    protected function setId(string $id): void
    {
        $this->id = $id;
    }

    protected function setType(string $type): void
    {
        $this->type = $type;
    }

    protected function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    protected function setRelationships(array $relationships): void
    {
        $this->relationships = $relationships;
    }

    protected function setLinks(array $links): void
    {
        $this->links = $links;
    }

    protected function writeUrlForRoute(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->routeCollector->getRouteParser()->urlFor($routeName, $data, $queryParams);
    }

    protected function writeDateTime(\DateTimeInterface $value): string
    {
        return Carbon::instance($value)->toIso8601String();
    }

    protected function applySparseFieldset(array $fields, array $requiredFields): array
    {
        if (count($requiredFields) <= 0) {
            // No sparse fieldset requested - return full resource
            return $fields;
        }

        // If a sparse fieldset has been defined for this entity type, apply it now
        $sparseAttribs = [];
        foreach ($requiredFields as $fieldName) {
            if (isset($fields[$fieldName]) == true) {
                $sparseAttribs[$fieldName] = $fields[$fieldName];
            }
        }
        return $sparseAttribs;
    }

    abstract protected function generateAttributes($entity, array $fields = []): array;

    abstract protected function generateRelationships($entity, array $relatedResources = []): array;

    abstract protected function generateLinks($entity): array;
}
