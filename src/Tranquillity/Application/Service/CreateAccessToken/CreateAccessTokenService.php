<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAccessToken;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Application\Service\Auth\CreateAccessTokenRequest;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\UserId;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Validation\Notification;

class CreateAccessTokenService implements ApplicationService
{
    private AccessTokenRepository $tokenRepository;
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private CreateAccessTokenDataTransformer $dataTransformer;

    public function __construct(
        AccessTokenRepository $tokenRepository,
        ClientRepository $clientRepository,
        UserRepository $userRepository,
        CreateAccessTokenDataTransformer $dataTransformer
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param CreateAccessTokenRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . CreateAccessTokenRequest::class . "' must be supplied to this service!");
        }

        // Check whether the user already exists
        $token = $this->tokenRepository->findByToken($request->token());
        if ($token != null) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_ACCESS_TOKEN_ALREADY_EXISTS,
                "An access token already exists for this value ({$request->token()})",
                'access_token',
                'token'
            );
            return $this->dataTransformer->read();
        }

        // Find IDs for supplied client and user
        $client = $this->clientRepository->findByName($request->clientName());
        if ($client == null) {
            return $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_CLIENT_DOES_NOT_EXIST,
                "No OAuth client exists with name '{$request->clientName()}'",
                'user'
            );
            return $this->dataTransformer->read();
        }

        // Create identity value objects
        $userId = $request->userId();
        if (is_null($userId) == false) {
            $userId = UserId::create($userId);
        }

        // Create new AccessToken entity
        try {
            $accessToken = new AccessToken(
                $this->tokenRepository->nextIdentity(),
                $request->token(),
                $client->id(),
                $userId,
                $request->expires(),
                $request->scope()
            );
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Persist the new AccessToken entity
        $this->tokenRepository->add($accessToken);

        // Write AccessToken entity to data transformer for consumption by calling client
        $this->dataTransformer->write($accessToken);
        return $this->dataTransformer->read();
    }

    /** @return mixed */
    private function exitWithError(string $code, string $detail, string $sourceType, string $sourceField)
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
