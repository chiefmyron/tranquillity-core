<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindRefreshTokenByToken;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Tranquillity\Domain\Validation\Notification;

class FindRefreshTokenByTokenService
{
    private RefreshTokenRepository $repository;
    private FindRefreshTokenByTokenDataTransformer $dataTransformer;

    public function __construct(RefreshTokenRepository $repository, FindRefreshTokenByTokenDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param FindRefreshTokenByTokenRequest $request
     * @return mixed
     */
    public function execute(FindRefreshTokenByTokenRequest $request)
    {
        // Get request details
        $token = $request->token();

        // Retrieve existing RefreshToken entity
        $refreshToken = $this->repository->findByToken($token);
        if (is_null($refreshToken) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_REFRESH_TOKEN_DOES_NOT_EXIST,
                "No refresh token exists with value '{$token}'",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($refreshToken);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithError(string $code, string $detail, string $sourceType = '', string $sourceField = '')
    {
        // Create validation notification
        $notification = new Notification();
        $notification->addItem($code, $detail, $sourceType, $sourceField);
        $this->dataTransformer->writeValidationError($notification);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithErrorCollection(array $errors)
    {
        // Create validation notification
        $notification = new Notification();
        $notification->addErrors($errors);
        $this->dataTransformer->writeValidationError($notification);
        return $this->dataTransformer->read();
    }
}
