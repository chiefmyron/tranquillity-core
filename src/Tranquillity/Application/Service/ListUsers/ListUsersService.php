<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ListUsers;

use Tranquillity\Domain\Model\Auth\UserRepository;

class ListUsersService
{
    private UserRepository $repository;
    private ListUsersDataTransformer $dataTransformer;

    public function __construct(UserRepository $repository, ListUsersDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ListUsersRequest $request
     * @return mixed
     */
    public function execute(ListUsersRequest $request)
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
