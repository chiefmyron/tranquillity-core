<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

use Tranquillity\Application\DataTransformer\Auth\UserDataTransformer;
use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Service\Auth\HashingService;
use Tranquillity\Domain\Model\Auth\Password;
use Tranquillity\Domain\Validation\Notification;

class CreateUserService implements ApplicationService
{
    private UserRepository $repository;
    private HashingService $hashingService;
    private UserDataTransformer $dataTransformer;

    public function __construct(UserRepository $repository, HashingService $hashingService, UserDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->hashingService = $hashingService;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param CreateUserRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . CreateUserRequest::class . "' must be supplied to this service!");
        }

        // Check whether the user already exists
        $existingUser = $this->repository->findByUsername($request->username());
        if ($existingUser != null) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::USER_ALREADY_EXISTS,
                "A user already exists for this username ({$request->username()})",
                'user',
                'username'
            );
            return $this->dataTransformer->read();
        }

        // Hash the password
        $password = new Password($request->password());
        $hashedPassword = $this->hashingService->hash($password);

        // Create new User entity
        try {
            $user = new User(
                $this->repository->nextIdentity(),
                $request->username(),
                $hashedPassword,
                $request->timezoneCode(),
                $request->localeCode(),
                $request->active()
            );
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Persist the new User entity
        $this->repository->add($user);

        // Write User entity to data transformer for consumption by calling client
        $this->dataTransformer->write($user);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithError(string $code, string $detail, string $sourceType, string $sourceField)
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
