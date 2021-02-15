<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

class ListPeopleRequest
{
    private array $filters;
    private array $sorting;
    private int $pageNumber;
    private int $pageSize;

    public function __construct(
        array $filters,
        array $sorting,
        int $pageNumber,
        int $pageSize
    ) {
        $this->filters = $filters;
        $this->sorting = $sorting;
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

    public function pageNumber(): int
    {
        return $this->pageNumber;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }
}
