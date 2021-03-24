<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Domain\Model\DomainEntityCollection;

abstract class AbstractResourceCollection
{
    private int $totalRecordCount;
    private int $pageNumber;
    private int $pageSize;
    private iterable $collection;
    protected RouteCollectorInterface $routeCollector;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function populate(DomainEntityCollection $collection, array $fields = [], array $relatedResources = []): void
    {
        // Generate array of resource objects
        $this->collection = $this->generateCollection($collection, $fields, $relatedResources);

        // Set pagination details
        $this->totalRecordCount = $collection->totalRecordCount();
        $this->pageNumber = $collection->pageNumber();
        $this->pageSize = $collection->pageSize();
    }

    public function render(): array
    {
        $data = [];
        foreach ($this->collection as $resourceObject) {
            $data[] = $resourceObject->render();
        }
        return $data;
    }

    public function totalRecordCount(): int
    {
        return $this->totalRecordCount;
    }

    public function pageNumber(): int
    {
        return $this->pageNumber;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }

    abstract protected function generateCollection($entityCollection, array $fields = [], array $relatedResources = []): array;
}
