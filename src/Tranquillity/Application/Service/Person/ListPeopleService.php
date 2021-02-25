<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\PersonRepository;

class ListPeopleService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ListPeopleRequest $request, PersonCollectionDataTransformer $dataTransformer): array
    {
        // Get request details
        $filters = $request->filters();
        $sorting = $request->sorting();
        $pageNumber = $request->pageNumber();
        $pageSize = $request->pageSize();

        // Get paginated list of people
        $peopleList = $this->repository->list($filters, $sorting, $pageNumber, $pageSize);
        $dataTransformer->write($peopleList);

        // Assemble the DTO for the response
        return $dataTransformer->read();
    }
}
