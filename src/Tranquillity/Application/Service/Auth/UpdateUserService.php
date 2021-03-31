<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

use Tranquillity\Application\DataTransformer\Auth\UserDataTransformer;
use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\UserId;
use Tranquillity\Domain\Model\Auth\UserRepository;

class UpdateUserService implements ApplicationService
{
    private UserRepository $repository;
    private UserDataTransformer $dataTransformer;

    public function __construct(UserRepository $repository, UserDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param UpdateUserRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("An '" . UpdateUserRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $id = $request->id();
        $fields = $request->fields();
        $relatedResources = $request->relatedResources();

        // Retrieve existing User entity
        $user = $this->repository->findById(UserId::create($id));
        if (is_null($user) == true) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::USER_DOES_NOT_EXIST,
                "No user exists with ID {$id}"
            );
            return $this->dataTransformer->read();
        }

        // Update User entity with new details
        foreach ($request->getUpdatedAttributes() as $attributeName) {
            $user->changeAttribute($attributeName, $request->$attributeName());
        }

        // Persist the new User entity
        $this->repository->update($user);

        // Write User entity to data transformer for consumption by calling client
        $this->dataTransformer->write($user, $fields, $relatedResources);
        return $this->dataTransformer->read();
    }
}
