<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\AuthorizationCodeInterface;
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeRequest;
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeService;
use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeRequest;
use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeService;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeRequest;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;

class AuthorizationCodeProvider implements AuthorizationCodeInterface
{
    private FindAuthorizationCodeByCodeService $viewService;
    private CreateAuthorizationCodeService $createService;
    private DeleteAuthorizationCodeService $deleteService;
    private TransactionalSession $txnSession;

    public function __construct(
        FindAuthorizationCodeByCodeService $viewService,
        CreateAuthorizationCodeService $createService,
        DeleteAuthorizationCodeService $deleteService,
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
    public function getAuthorizationCode($code)
    {
        $request = new FindAuthorizationCodeByCodeRequest($code);
        return $this->viewService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
    {
        // Build service request
        $request = new CreateAuthorizationCodeRequest(
            $code,
            $client_id,
            $user_id,
            (new \DateTime())->setTimestamp($expires),
            $redirect_uri,
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
    public function expireAuthorizationCode($code)
    {
        // Build service request
        $request = new DeleteAuthorizationCodeRequest($code);

        // Execute as a transaction
        $txnService = new TransactionalService($this->deleteService, $this->txnSession);
        $txnService->execute($request);
        return;
    }
}
