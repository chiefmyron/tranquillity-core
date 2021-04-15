<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateRefreshToken;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\RefreshToken;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Validation\Notification;

class CreateRefreshTokenService implements ApplicationService
{
    private RefreshTokenRepository $tokenRepository;
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private CreateRefreshTokenDataTransformer $dataTransformer;

    public function __construct(
        RefreshTokenRepository $tokenRepository,
        ClientRepository $clientRepository,
        UserRepository $userRepository,
        CreateRefreshTokenDataTransformer $dataTransformer
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param CreateRefreshTokenRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . CreateRefreshTokenRequest::class . "' must be supplied to this service!");
        }

        // Check whether the token already exists
        $token = $this->tokenRepository->findByToken($request->token());
        if ($token != null) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_REFRESH_TOKEN_ALREADY_EXISTS,
                "A refresh token already exists for this value ({$request->token()})",
                'refresh_token',
                'token'
            );
            return $this->dataTransformer->read();
        }

        // Find specified OAuth Client
        $client = $this->clientRepository->findByName($request->clientName());
        if ($client == null) {
            return $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_CLIENT_DOES_NOT_EXIST,
                "No OAuth client exists with name '{$request->clientName()}'",
                'user'
            );
            return $this->dataTransformer->read();
        }

        // Find specified user (if provided)
        $user = null;
        if ($request->username() != null) {
            $user = $this->userRepository->findByUsername($request->username());
        }

        // Create new RefreshToken entity
        try {
            $refreshToken = new RefreshToken(
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

        // Persist the new RefreshToken entity
        $this->tokenRepository->add($refreshToken);

        // Write RefreshToken entity to data transformer for consumption by calling client
        $this->dataTransformer->write($refreshToken);
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
