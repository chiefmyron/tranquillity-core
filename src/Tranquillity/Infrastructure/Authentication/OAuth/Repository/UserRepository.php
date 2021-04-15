<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameRequest;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameService;
use Tranquillity\Domain\Service\Auth\VerifyUserCredentialsService;

class UserRepository implements UserRepositoryInterface
{
    private FindUserByUsernameService $viewService;
    private VerifyUserCredentialsService $verifyService;

    public function __construct(FindUserByUsernameService $viewService, VerifyUserCredentialsService $verifyService)
    {
        $this->viewService = $viewService;
        $this->verifyService = $verifyService;
    }

    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        // Validate credentials
        $valid = $this->verifyService->validate($username, $password);
        if ($valid === false) {
            return null;
        }

        // If credentials are valid, return user details
        $request = new FindUserByUsernameRequest($username);
        return $this->viewService->execute($request);
    }
}
