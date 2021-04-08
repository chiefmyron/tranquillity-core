<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenDataTransformer;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenDataTransformer;
use Tranquillity\Domain\Model\Auth\RefreshToken;
use Tranquillity\Domain\Validation\Notification;

class RefreshTokenDataTransformer implements
    FindRefreshTokenByTokenDataTransformer,
    CreateRefreshTokenDataTransformer
{
    private array $data = [];

    public function write(RefreshToken $entity): void
    {
        $this->data = [
            'refresh_token' => $entity->token(),
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
