<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Exception\Person\PersonDoesNotExistException;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Domain\Validation\ValidationException;

class UpdatePersonService implements ApplicationService
{
    private PersonRepository $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UpdatePersonRequest $request
     * @return mixed
     */
    public function execute($request = null, $dataTransformer = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \Exception("An '" . UpdatePersonRequest::class . "' must be supplied to this service!");
        }

        // Retrieve existing Person entity
        $person = $this->repository->findById(PersonId::create($request->id()));
        if (is_null($person) == true) {
            throw new PersonDoesNotExistException("No person exists with ID {$request->id()}", 'pointer', '/data/id');
        }

        // Update Person entity with new details
        foreach ($request->getUpdatedAttributes() as $attributeName) {
            $person->changeAttribute($attributeName, $request->$attributeName());
        }

        // Persist the new Person entity
        $this->repository->update($person);

        // Write Person entity to data transformer for consumption by calling client
        $dataTransformer->write($person);
        return $dataTransformer->read();
    }
}
