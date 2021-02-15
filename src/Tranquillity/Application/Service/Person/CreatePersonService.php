<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;

class CreatePersonService implements ApplicationService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreatePersonRequest $request
     * @return mixed
     */
    public function execute($request = null, $dataTransformer = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \Exception("A '" . CreatePersonRequest::class . "' must be supplied to this service!");
        }

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
        $dataTransformer->write($person);
        return $dataTransformer->read();
    }
}
