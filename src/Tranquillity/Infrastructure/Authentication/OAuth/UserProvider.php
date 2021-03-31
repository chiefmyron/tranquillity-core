<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth;

use OAuth2\Storage\UserCredentialsInterface;
use Tranquillity\Application\Service\Auth\ViewUserByUsernameService;
use Tranquillity\Application\Service\Auth\ViewUserByUsernameRequest;
use Tranquillity\Domain\Service\Auth\VerifyUserCredentialsService;

class UserProvider implements UserCredentialsInterface
{
    private ViewUserByUsernameService $viewService;
    private VerifyUserCredentialsService $verifyService;

    public function __construct(ViewUserByUsernameService $viewService, VerifyUserCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    /**
     * @inheritDoc
     */
    public function getUserDetails($username): array
    {
        $request = new ViewUserByUsernameRequest($username);
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
