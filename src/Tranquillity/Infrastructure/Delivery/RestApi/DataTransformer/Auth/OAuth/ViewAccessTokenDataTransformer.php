<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\DataTransformer\Auth\AccessTokenDataTransformer;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Validation\Notification;

class ViewAccessTokenDataTransformer implements AccessTokenDataTransformer
{
    private array $data = [];

    public function write(AccessToken $entity): void
    {
        $this->data = [
            'token' => $entity->token(),
            'client_id' => $entity->getClientIdValue(),
            'user_id' => $entity->getUserIdValue(),
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