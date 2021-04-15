<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Repository;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenRequest;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenService;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenRequest;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenService;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenRequest;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\RefreshToken;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private FindRefreshTokenByTokenService $viewService;
    private CreateRefreshTokenService $createService;
    private DeleteRefreshTokenService $deleteService;
    private TransactionalSession $txnSession;

    public function __construct(
        FindRefreshTokenByTokenService $viewService,
        CreateRefreshTokenService $createService,
        DeleteRefreshTokenService $deleteService,
        TransactionalSession $txn
    ) {
        $this->viewService = $viewService;
        $this->createService = $createService;
        $this->deleteService = $deleteService;
        $this->txnSession = $txn;
    }

    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $request = new CreateRefreshTokenRequest(
            $refreshTokenEntity->getIdentifier(),
            $refreshTokenEntity->getAccessToken()->getClient()->getName(),
            $refreshTokenEntity->getAccessToken()->getUserIdentifier(),
            $refreshTokenEntity->getExpiryDateTime(),
            $refreshTokenEntity->getAccessToken()->getScopes()
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->createService, $this->txnSession);
        $txnService->execute($request);
    }

    public function revokeRefreshToken($tokenId): void
    {
        // Build service request
        $request = new DeleteRefreshTokenRequest($tokenId);

        // Execute as a transaction
        $txnService = new TransactionalService($this->deleteService, $this->txnSession);
        $txnService->execute($request);
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        $request = new FindRefreshTokenByTokenRequest($tokenId);
        $token = $this->viewService->execute($request);
        if ($token instanceof RefreshToken) {
            return false;
        }
        return true;
    }
}
