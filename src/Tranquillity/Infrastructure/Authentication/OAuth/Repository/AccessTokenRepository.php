<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Repository;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Tranquillity\Application\Service\CreateAccessToken\CreateAccessTokenRequest;
use Tranquillity\Application\Service\CreateAccessToken\CreateAccessTokenService;
use Tranquillity\Application\Service\DeleteAccessToken\DeleteAccessTokenRequest;
use Tranquillity\Application\Service\DeleteAccessToken\DeleteAccessTokenService;
use Tranquillity\Application\Service\FindAccessTokenByToken\FindAccessTokenByTokenRequest;
use Tranquillity\Application\Service\FindAccessTokenByToken\FindAccessTokenByTokenService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\AccessToken;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private FindAccessTokenByTokenService $viewService;
    private CreateAccessTokenService $createService;
    private DeleteAccessTokenService $deleteService;
    private TransactionalSession $txnSession;

    public function __construct(
        FindAccessTokenByTokenService $viewService,
        CreateAccessTokenService $createService,
        DeleteAccessTokenService $deleteService,
        TransactionalSession $txn
    ) {
        $this->viewService = $viewService;
        $this->createService = $createService;
        $this->deleteService = $deleteService;
        $this->txnSession = $txn;
    }

    /**
     * @inheritDoc
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        //return new AccessToken($clientEntity, $scopes, $userIdentifier);
        $accessToken = new AccessToken();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        // Convert scope entity array to a simple array of identifiers
        $scopes = [];
        foreach ($accessTokenEntity->getScopes() as $scopeEntity) {
            $scopes[] = $scopeEntity->getIdentifier();
        }

        $request = new CreateAccessTokenRequest(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getClient()->getIdentifier(), // Actually client name
            (string)$accessTokenEntity->getUserIdentifier(),  // Actually username
            $accessTokenEntity->getExpiryDateTime(),
            $scopes
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->createService, $this->txnSession);
        $txnService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function revokeAccessToken($tokenId): void
    {
        // Build service request
        $request = new DeleteAccessTokenRequest($tokenId);

        // Execute as a transaction
        $txnService = new TransactionalService($this->deleteService, $this->txnSession);
        $txnService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function isAccessTokenRevoked($tokenId): bool
    {
        $request = new FindAccessTokenByTokenRequest($tokenId);
        $token = $this->viewService->execute($request);
        if ($token instanceof AccessToken) {
            return false;
        }
        return true;
    }
}
