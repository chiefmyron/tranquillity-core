<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameRequest;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameService;
use Tranquillity\Domain\Service\Auth\VerifyClientCredentialsService;

class ClientRepository implements ClientRepositoryInterface
{
    private FindClientByNameService $viewService;
    private VerifyClientCredentialsService $verifyService;

    public function __construct(FindClientByNameService $viewService, VerifyClientCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    public function getClientEntity($clientIdentifier): ClientEntityInterface
    {
        $request = new FindClientByNameRequest($clientIdentifier);
        return $this->viewService->execute($request);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        return $this->verifyService->validate($clientIdentifier, $clientSecret);
    }
}
