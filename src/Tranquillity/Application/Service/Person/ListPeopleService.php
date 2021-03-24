<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\Person\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\PersonRepository;

class ListPeopleService
{
    private PersonRepository $repository;
    private PersonCollectionDataTransformer $dataTransformer;

    public function __construct(PersonRepository $repository, PersonCollectionDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ListPeopleRequest $request
     * @return mixed
     */
    public function execute(ListPeopleRequest $request)
    {
        // Get request details
        $filters = $request->filters();
        $sorting = $request->sorting();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();
        $pageNumber = $request->pageNumber();
        $pageSize = $request->pageSize();

        // Get paginated list of people
        $peopleList = $this->repository->list($filters, $sorting, $pageNumber, $pageSize);
        $this->dataTransformer->write($peopleList, $fields, $relatedResources);

        // Assemble the DTO for the response
        return $this->dataTransformer->read();
    }
}
