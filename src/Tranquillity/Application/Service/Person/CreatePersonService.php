<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;

class CreatePersonService implements ApplicationService
{
    private PersonRepository $repository;
    private PersonDataTransformer $dataTransformer;

    public function __construct(PersonRepository $repository, PersonDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param CreatePersonRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \Exception("A '" . CreatePersonRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Create new Person entity
        $person = new Person(
            $this->repository->nextIdentity(),
            $request->firstName(),
            $request->lastName(),
            $request->jobTitle(),
            $request->emailAddress()
        );

        // Persist the new Person entity
        $this->repository->add($person);

        // Write Person entity to data transformer for consumption by calling client
        $this->dataTransformer->write($person, $fields, $relatedResources);
        return $this->dataTransformer->read();
    }
}
