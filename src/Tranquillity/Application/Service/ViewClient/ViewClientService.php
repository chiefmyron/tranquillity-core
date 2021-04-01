<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ViewClient;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\ClientId;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Validation\Notification;

class ViewClientService
{
    private ClientRepository $repository;
    private ViewClientDataTransformer $dataTransformer;

    public function __construct(ClientRepository $repository, ViewClientDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ViewClientRequest $request
     * @return mixed
     */
    public function execute(ViewClientRequest $request)
    {
        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Retrieve existing Client entity
        $client = $this->repository->findById(ClientId::create($id));
        if (is_null($client) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_CLIENT_DOES_NOT_EXIST,
                "No OAuth client exists with ID {$id}",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($client, $fields, $relatedResources);
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
