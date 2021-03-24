<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\User;

class ListUsersRequest
{
    private array $filters;
    private array $sorting;
    private array $fields;
    private array $relatedResources;
    private int $pageNumber;
    private int $pageSize;

    public function __construct(
        array $filters,
        array $sorting,
        array $fields,
        array $relatedResources,
        int $pageNumber,
        int $pageSize
    ) {
        $this->filters = $filters;
        $this->sorting = $sorting;
        $this->fields = $fields;
        $this->relatedResources = $relatedResources;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function sorting(): array
    {
        return $this->sorting;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function relatedResources(): array
    {
        return $this->relatedResources;
    }

    public function pageNumber(): int
    {
        return $this->pageNumber;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }
}
