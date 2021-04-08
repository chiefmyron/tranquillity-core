<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteRefreshToken;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Tranquillity\Domain\Validation\Notification;

class DeleteRefreshTokenService implements ApplicationService
{
    private RefreshTokenRepository $repository;
    private DeleteRefreshTokenDataTransformer $dataTransformer;

    public function __construct(
        RefreshTokenRepository $repository,
        DeleteRefreshTokenDataTransformer $dataTransformer
    ) {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param DeleteRefreshTokenRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . DeleteRefreshTokenRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $token = $request->token();

        // Retrieve existing RefreshToken entity
        $refreshToken = $this->repository->findByToken($token);
        if (is_null($refreshToken) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_REFRESH_TOKEN_DOES_NOT_EXIST,
                "No refresh token exists with value '{$token}'",
                'refresh_token'
            );
        }

        // Create to delete the RefreshToken entity
        try {
            $this->repository->remove($refreshToken);
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Write RefreshToken entity to data transformer for consumption by calling client
        $this->dataTransformer->write();
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
