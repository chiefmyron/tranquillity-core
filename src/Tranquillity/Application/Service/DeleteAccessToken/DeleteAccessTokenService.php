<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteAccessToken;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Validation\Notification;

class DeleteAccessTokenService implements ApplicationService
{
    private AccessTokenRepository $repository;
    private DeleteAccessTokenDataTransformer $dataTransformer;

    public function __construct(
        AccessTokenRepository $repository,
        DeleteAccessTokenDataTransformer $dataTransformer
    ) {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param DeleteAccessTokenRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . DeleteAccessTokenRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $token = $request->token();

        // Retrieve existing AccessToken entity
        $accessToken = $this->repository->findByToken($token);
        if (is_null($accessToken) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_ACCESS_TOKEN_DOES_NOT_EXIST,
                "No access token exists with value '{$token}'",
                'access_token'
            );
        }

        // Create to delete the AccessToken entity
        try {
            $this->repository->remove($accessToken);
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Write AccessToken entity to data transformer for consumption by calling client
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
