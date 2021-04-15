<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Repository;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeRequest;
use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeService;
use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeRequest;
use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeService;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeRequest;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\AuthorizationCode;

class AuthorizationCodeRepository implements AuthCodeRepositoryInterface
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

    public function getNewAuthCode()
    {
        return new AuthorizationCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        // Build service request
        $request = new CreateAuthorizationCodeRequest(
            $authCodeEntity->getIdentifier(),
            $authCodeEntity->getClient()->getIdentifier(),
            $authCodeEntity->getUserIdentifier(),
            $authCodeEntity->getExpiryDateTime(),
            $authCodeEntity->getRedirectUri(),
            $authCodeEntity->getScopes()
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->createService, $this->txnSession);
        $txnService->execute($request);
        return;
    }

    public function revokeAuthCode($codeId): void
    {
        // Build service request
        $request = new DeleteAuthorizationCodeRequest($codeId);

        // Execute as a transaction
        $txnService = new TransactionalService($this->deleteService, $this->txnSession);
        $txnService->execute($request);
        return;
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        $request = new FindAuthorizationCodeByCodeRequest($codeId);
        return $this->viewService->execute($request);
    }
}
