<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindAuthorizationCodeByCode;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Tranquillity\Domain\Validation\Notification;

class FindAuthorizationCodeByCodeService
{
    private AuthorizationCodeRepository $repository;
    private FindAuthorizationCodeByCodeDataTransformer $dataTransformer;

    public function __construct(AuthorizationCodeRepository $repository, FindAuthorizationCodeByCodeDataTransformer $dataTransformer)
    {
        $this->repository = $repository;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @param FindRefreshTokenByTokenRequest $request
     * @return mixed
     */
    public function execute(FindAuthorizationCodeByCodeRequest $request)
    {
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

        // Assemble the DTO for the response
        $this->dataTransformer->write($authCode);
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
