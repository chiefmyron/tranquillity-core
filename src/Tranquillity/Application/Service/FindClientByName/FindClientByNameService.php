<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindClientByName;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Validation\Notification;

class FindClientByNameService
{
    private ClientRepository $repository;
    private FindClientByNameDataTransformer $dataTransformer;

    public function __construct(ClientRepository $repository, FindClientByNameDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param FindClientByNameRequest $request
     * @return mixed
     */
    public function execute(FindClientByNameRequest $request)
    {
        // Get request details
        $name = $request->name();

        // Retrieve existing Client entity
        $client = $this->repository->findByName($name);
        if (is_null($client) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_CLIENT_DOES_NOT_EXIST,
                "No OAuth client exists with name '{$name}'",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($client);
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
