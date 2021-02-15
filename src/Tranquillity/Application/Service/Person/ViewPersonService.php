<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\PersonDataTransformer;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;

class ViewPersonService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ViewPersonRequest $request
     * @param PersonDataTransformer $dataTransformer
     * @return mixed
     */
    public function execute(ViewPersonRequest $request, PersonDataTransformer $dataTransformer)
    {
        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Get paginated list of people
        $person = $this->repository->findById(PersonId::create($id));
        $dataTransformer->write($person, $fields, $relatedResources);

        // Assemble the DTO for the response
        return $dataTransformer->read();
    }
}
