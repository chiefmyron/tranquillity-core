<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\User;

use Tranquillity\Application\DataTransformer\User\UserDataTransformer;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\User\UserId;
use Tranquillity\Domain\Model\User\UserRepository;
use Tranquillity\Domain\Validation\Notification;

class ViewUserService
{
    private UserRepository $repository;
    private UserDataTransformer $dataTransformer;

    public function __construct(UserRepository $repository, UserDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ViewUserRequest $request
     * @return mixed
     */
    public function execute(ViewUserRequest $request)
    {
        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Retrieve existing User entity
        $user = $this->repository->findById(UserId::create($id));
        if (is_null($user) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::USER_DOES_NOT_EXIST,
                "No user exists with ID {$id}",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($user, $fields, $relatedResources);
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
