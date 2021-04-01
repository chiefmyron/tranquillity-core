<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\ClientCredentialsInterface;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameRequest;
use Tranquillity\Application\Service\FindClientByName\FindClientByNameService;
use Tranquillity\Domain\Service\Auth\VerifyClientCredentialsService;

class ClientProvider implements ClientCredentialsInterface
{
    private FindClientByNameService $viewService;
    private VerifyClientCredentialsService $verifyService;

    public function __construct(FindClientByNameService $viewService, VerifyClientCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    /**
     * @inheritDoc
     */
    public function getClientDetails($client_id): array
    {
        $request = new FindClientByNameRequest($client_id);
        return $this->viewService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function getClientScope($client_id): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function checkRestrictedGrantType($client_id, $grant_type): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function checkClientCredentials($client_id, $client_secret = null): bool
    {
        return $this->verifyService->validate($client_id, $client_secret);
    }

    /**
     * @inheritDoc
     */
    public function isPublicClient($client_id): bool
    {
        return false;
    }
}
