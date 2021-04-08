<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteAuthorizationCode;

use Tranquillity\Application\Service\ApplicationService;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Tranquillity\Domain\Validation\Notification;

class DeleteAuthorizationCodeService implements ApplicationService
{
    private AuthorizationCodeRepository $repository;
    private DeleteAuthorizationCodeDataTransformer $dataTransformer;

    public function __construct(
        AuthorizationCodeRepository $repository,
        DeleteAuthorizationCodeDataTransformer $dataTransformer
    ) {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param DeleteAuthorizationCodeRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        // Make sure request has been provided for this service
        if (is_null($request) == true) {
            throw new \InvalidArgumentException("A '" . DeleteAuthorizationCodeRequest::class . "' must be supplied to this service!");
        }

        // Get request details
        $code = $request->code();

        // Retrieve existing AuthorizationCode entity
        $authCode = $this->repository->findByCode($code);
        if (is_null($authCode) == true) {
            return $this->exitWithError(
                ErrorCodeEnum::OAUTH_AUTHORIZATION_CODE_DOES_NOT_EXIST,
                "No authorization code exists with value '{$code}'",
                'authorization_code'
            );
        }

        // Create to delete the AuthorizationCode entity
        try {
            $this->repository->remove($authCode);
        } catch (ValidationException $ex) {
            // Write notifications out as errors
            return $this->exitWithErrorCollection($ex->getErrors());
        }

        // Write AuthorizationCode entity to data transformer for consumption by calling client
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
