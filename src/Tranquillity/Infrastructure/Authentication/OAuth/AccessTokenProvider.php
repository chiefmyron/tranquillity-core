<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\AccessTokenInterface;
use Tranquillity\Application\Service\Auth\CreateAccessTokenRequest;
use Tranquillity\Application\Service\Auth\ViewAccessTokenByTokenRequest;
use Tranquillity\Application\Service\Auth\ViewAccessTokenByTokenService;
use Tranquillity\Application\Service\Auth\CreateAccessTokenService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;

class AccessTokenProvider implements AccessTokenInterface
{
    private ViewAccessTokenByTokenService $viewService;
    private CreateAccessTokenService $createService;
    private TransactionalSession $txnSession;

    public function __construct(ViewAccessTokenByTokenService $viewService, CreateAccessTokenService $createService, TransactionalSession $txn)
    {
        $this->viewService = $viewService;
        $this->createService = $createService;
        $this->txnSession = $txn;
    }

    /**
     * @inheritDoc
     */
    public function getAccessToken($oauth_token)
    {
        $request = new ViewAccessTokenByTokenRequest($oauth_token);
        return $this->viewService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
    {
        $request = new CreateAccessTokenRequest(
            $oauth_token,
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
}
