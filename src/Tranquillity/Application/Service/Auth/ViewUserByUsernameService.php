<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

use Tranquillity\Application\DataTransformer\Auth\UserDataTransformer;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Validation\Notification;

class ViewUserByUsernameService
{
    private UserRepository $repository;
    private UserDataTransformer $dataTransformer;

    public function __construct(UserRepository $repository, UserDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ViewUserByUsernameRequest $request
     * @return mixed
     */
    public function execute(ViewUserByUsernameRequest $request)
    {
        // Get request details
        $username = $request->username();

        // Retrieve existing User entity
        $user = $this->repository->findByUsername($username);
        if (is_null($user) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::USER_DOES_NOT_EXIST,
                "No user exists with username '{$username}'",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($user);
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
