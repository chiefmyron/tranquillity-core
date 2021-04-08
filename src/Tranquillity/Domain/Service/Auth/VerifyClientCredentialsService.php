<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Service\Auth;

use Tranquillity\Domain\Event\Auth\ClientCredentialValidationAttempted;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\Password;

class VerifyClientCredentialsService
{
    private ClientRepository $repository;
    private HashingService $hashingService;

    public function __construct(ClientRepository $repository, HashingService $hashingService)
    {
        $this->repository = $repository;
        $this->hashingService = $hashingService;
    }

    public function validate(string $clientName, string $clientSecret): bool
    {
        // Publish client credential validation attempt
        DomainEventPublisher::instance()->publish(
            new ClientCredentialValidationAttempted($clientName)
        );

        // Retrieve the specified client
        $client = $this->repository->findByName($clientName);

        // Validate the client secret
        $clientPassword = new Password($clientSecret);
        return $this->hashingService->verify($clientPassword, $client->password());
    }
}
