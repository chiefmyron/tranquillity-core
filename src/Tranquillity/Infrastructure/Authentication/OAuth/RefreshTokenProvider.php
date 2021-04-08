<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\RefreshTokenInterface;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenRequest;
use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenService;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenRequest;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenService;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenRequest;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;

class RefreshTokenProvider implements RefreshTokenInterface
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

    /**
     * @inheritDoc
     */
    public function getRefreshToken($refreshToken)
    {
        $request = new FindRefreshTokenByTokenRequest($refreshToken);
        return $this->viewService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // Build service request
        $request = new CreateRefreshTokenRequest(
            $refresh_token,
            $client_id,
            $user_id,
            (new \DateTime())->setTimestamp($expires),
            $scope
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->createService, $this->txnSession);
        $txnService->execute($request);
        return;
    }

    /**
     * @inheritDoc
     */
    public function unsetRefreshToken($refresh_token)
    {
        // Build service request
        $request = new DeleteRefreshTokenRequest($refresh_token);

        // Execute as a transaction
        $txnService = new TransactionalService($this->deleteService, $this->txnSession);
        $txnService->execute($request);
        return;
    }
}
