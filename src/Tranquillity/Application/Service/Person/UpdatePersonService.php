<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;

class UpdatePersonService implements ApplicationService
{
    private PersonRepository $repository;
    private PersonDataTransformer $dataTransformer;

    public function __construct(PersonRepository $repository, PersonDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param UpdatePersonRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("An '" . UpdatePersonRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Retrieve existing Person entity
        $person = $this->repository->findById(PersonId::create($id));
        if (is_null($person) == true) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::PERSON_DOES_NOT_EXIST,
                "No person exists with ID {$id}"
            );
            return $this->dataTransformer->read();
        }

        // Update Person entity with new details
        foreach ($request->getUpdatedAttributes() as $attributeName) {
            $person->changeAttribute($attributeName, $request->$attributeName());
        }

        // Persist the new Person entity
        $this->repository->update($person);

        // Write Person entity to data transformer for consumption by calling client
        $this->dataTransformer->write($person, $fields, $relatedResources);
        return $this->dataTransformer->read();
    }
}
