<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Person;

class PersonCollection
{
    private int $totalRecordCount;
    private int $pageNumber;
    private int $pageSize;
    private iterable $collection;

    public function __construct(iterable $collection, int $totalRecordCount, int $pageNumber, int $pageSize)
    {
        $this->collection = $collection;
        $this->totalRecordCount = $totalRecordCount;
        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
    }

    public function collection(): iterable
    {
        return $this->collection;
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
}
