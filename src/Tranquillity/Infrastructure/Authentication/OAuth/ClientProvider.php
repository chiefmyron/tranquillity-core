<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\ClientCredentialsInterface;
use Tranquillity\Application\Service\Auth\ViewClientByNameService;
use Tranquillity\Application\Service\Auth\ViewClientByNameRequest;
use Tranquillity\Domain\Service\Auth\VerifyClientCredentialsService;

class ClientProvider implements ClientCredentialsInterface
{
    private ViewClientByNameService $viewService;
    private VerifyClientCredentialsService $verifyService;

    public function __construct(ViewClientByNameService $viewService, VerifyClientCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    /**
     * @inheritDoc
     */
    public function getClientDetails($client_id): array
    {
        $request = new ViewClientByNameRequest($client_id);
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
