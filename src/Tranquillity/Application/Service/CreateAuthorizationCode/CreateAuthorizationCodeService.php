<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAuthorizationCode;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\AuthorizationCode;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Validation\Notification;

class CreateAuthorizationCodeService implements ApplicationService
{
    private AuthorizationCodeRepository $codeRepository;
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private CreateAuthorizationCodeDataTransformer $dataTransformer;

    public function __construct(
        AuthorizationCodeRepository $codeRepository,
        ClientRepository $clientRepository,
        UserRepository $userRepository,
        CreateAuthorizationCodeDataTransformer $dataTransformer
    ) {
        $this->codeRepository = $codeRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param CreateAuthorizationCodeRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . CreateAuthorizationCodeRequest::class . "' must be supplied to this service!");
        }

        // Check whether the code already exists
        $code = $this->codeRepository->findByCode($request->code());
        if ($code != null) {
            $this->dataTransformer->writeError(
                ErrorCodeEnum::OAUTH_AUTHORIZATION_CODE_ALREADY_EXISTS,
                "An authorization code already exists for this value ({$request->code()})",
                'authorization_code',
                'code'
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

        // Create new AuthorizationCode entity
        try {
            $code = new AuthorizationCode(
                $this->codeRepository->nextIdentity(),
                $request->code(),
                $client,
                $user,
                $request->expires(),
                $request->redirectUri(),
                $request->scopes()
            );
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Persist the new AuthorizationCode entity
        $this->tokenRepository->add($code);

        // Write AuthorizationCode entity to data transformer for consumption by calling client
        $this->dataTransformer->write($code);
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
