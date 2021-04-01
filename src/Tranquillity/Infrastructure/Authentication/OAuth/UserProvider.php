<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\UserCredentialsInterface;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameRequest;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameService;
use Tranquillity\Domain\Service\Auth\VerifyUserCredentialsService;

class UserProvider implements UserCredentialsInterface
{
    private FindUserByUsernameService $viewService;
    private VerifyUserCredentialsService $verifyService;

    public function __construct(FindUserByUsernameService $viewService, VerifyUserCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    /**
     * @inheritDoc
     */
    public function getUserDetails($username): array
    {
        $request = new FindUserByUsernameRequest($username);
        return $this->viewService->execute($request);
    }

    /**
     * @inheritDoc
     */
    public function checkUserCredentials($username, $password): bool
    {
        return $this->verifyService->validate($username, $password);
    }
}
