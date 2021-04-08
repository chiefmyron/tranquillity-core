<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\CreateAuthorizationCode\CreateAuthorizationCodeDataTransformer;
use Tranquillity\Application\Service\FindAuthorizationCodeByCode\FindAuthorizationCodeByCodeDataTransformer;
use Tranquillity\Domain\Model\Auth\AuthorizationCode;
use Tranquillity\Domain\Validation\Notification;

class AuthorizationCodeDataTransformer implements
    FindAuthorizationCodeByCodeDataTransformer,
    CreateAuthorizationCodeDataTransformer
{
    private array $data = [];

    public function write(AuthorizationCode $entity): void
    {
        $this->data = [
            'code' => $entity->code(),
            'client_id' => $entity->getClientName(),
            'user_id' => $entity->getUserUsername(),
            'expires' => $entity->expires()->getTimestamp(),
            'scope' => $entity->scope()
        ];
    }

    public function read(): array
    {
        return $this->data;
    }

    public function writeError(string $code, string $detail, string $source = '', string $field = '', array $meta = []): void
    {
        $this->data = [];
    }

    public function writeValidationError(Notification $notification): void
    {
        $this->data = [];
    }
}
