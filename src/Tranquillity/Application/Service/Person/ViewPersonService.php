<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;

class ViewPersonService
{
    private PersonRepository $repository;
    private PersonDataTransformer $dataTransformer;

    public function __construct(PersonRepository $repository, PersonDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ViewPersonRequest $request
     * @return mixed
     */
    public function execute(ViewPersonRequest $request)
    {
        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Get paginated list of people
        $person = $this->repository->findById(PersonId::create($id));

        // Retrieve existing Person entity
        $person = $this->repository->findById(PersonId::create($request->id()));
        if (is_null($person) == true) {
            throw new \InvalidArgumentException("No person exists with ID {$request->id()}");
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($person, $fields, $relatedResources);
        return $this->dataTransformer->read();
    }
}
