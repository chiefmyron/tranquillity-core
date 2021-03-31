<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

use Tranquillity\Application\DataTransformer\Auth\AccessTokenDataTransformer;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Validation\Notification;

class ViewAccessTokenByTokenService
{
    private AccessTokenRepository $repository;
    private AccessTokenDataTransformer $dataTransformer;

    public function __construct(AccessTokenRepository $repository, AccessTokenDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param ViewAccessTokenByTokenRequest $request
     * @return mixed
     */
    public function execute(ViewAccessTokenByTokenRequest $request)
    {
        // Get request details
        $token = $request->token();

        // Retrieve existing AccessToken entity
        $accessToken = $this->repository->findByToken($token);
        if (is_null($accessToken) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_ACCESS_TOKEN_DOES_NOT_EXIST,
                "No access token exists with value '{$token}'",
                'user'
            );
        }

        // Assemble the DTO for the response
        $this->dataTransformer->write($accessToken);
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
