<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Person;

use Tranquillity\Application\DataTransformer\PersonDataTransformer;
use Tranquillity\Domain\Exception\Person\PersonDoesNotExistException;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Domain\Validation\ValidationException;

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

        // Retrieve existing Person entity
        $person = $this->repository->findById(PersonId::create($request->id()));
        if (is_null($person) == true) {
            throw new PersonDoesNotExistException("No person exists with ID {$request->id()}", 'pointer', '/data/id');
        }

        // Assemble the DTO for the response
        $dataTransformer->write($person, $fields, $relatedResources);
        return $dataTransformer->read();
    }
}
