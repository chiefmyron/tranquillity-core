<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAccessToken;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\ClientId;
use Tranquillity\Domain\Model\Auth\ClientRepository;
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

        // Find specified OAuth Client
        $client = $this->clientRepository->findById(ClientId::create($request->clientId()));
        if ($client == null) {
            return $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_CLIENT_DOES_NOT_EXIST,
                "No OAuth client exists with ID '{$request->clientId()}'",
                'user'
            );
            return $this->dataTransformer->read();
        }

        // Find specified user (if provided)
        $user = null;
        if ($request->username() != null) {
            $user = $this->userRepository->findByUsername($request->username());
        }

        // Create new AccessToken entity
        try {
            $accessToken = new AccessToken(
                $this->tokenRepository->nextIdentity(),
                $request->token(),
                $client,
                $user,
                $request->expires(),
                $request->scopes()
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
