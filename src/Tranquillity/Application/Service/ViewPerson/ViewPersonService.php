<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ViewPerson;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Domain\Validation\Notification;

class ViewPersonService
{
    private PersonRepository $repository;
    private ViewPersonDataTransformer $dataTransformer;

    public function __construct(PersonRepository $repository, ViewPersonDataTransformer $dataTransformer)
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

        // Retrieve existing Person entity
        $person = $this->repository->findById(PersonId::create($id));
        if (is_null($person) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::PERSON_DOES_NOT_EXIST,
                "No person exists with ID {$id}",
                'person'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($person, $fields, $relatedResources);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithError(string $code, string $detail, string $sourceType = '', string $sourceField = '')
    {
        // Create validation notification
        $notification = new Notification();
        $notification->addItem($code, $detail, $sourceType, $sourceField);
        $this->dataTransformer->writeValidationError($notification);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithErrorCollection(array $errors)
    {
        // Create validation notification
        $notification = new Notification();
        $notification->addErrors($errors);
        $this->dataTransformer->writeValidationError($notification);
        return $this->dataTransformer->read();
    }
}
