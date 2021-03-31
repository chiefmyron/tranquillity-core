<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Service\Auth;

use Tranquillity\Domain\Event\Auth\UserCredentialValidationAttempted;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Model\Auth\Password;
use Tranquillity\Domain\Model\Auth\UserRepository;

class VerifyUserCredentialsService
{
    private UserRepository $repository;
    private HashingService $hashingService;

    public function __construct(UserRepository $repository, HashingService $hashingService)
    {
        $this->repository = $repository;
        $this->hashingService = $hashingService;
    }

    public function validate(string $username, string $password): bool
    {
        // Publish user credential validation attempt
        DomainEventPublisher::instance()->publish(
            new UserCredentialValidationAttempted($username)
        );

        // Retrieve the specified user
        $user = $this->repository->findByUsername($username);

        // Validate the user password
        $userPassword = new Password($password);
        return $this->hashingService->verify($userPassword, $user->password());
    }
}
