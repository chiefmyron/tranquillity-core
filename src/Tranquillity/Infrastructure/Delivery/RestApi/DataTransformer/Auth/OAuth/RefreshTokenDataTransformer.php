<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\CreateRefreshToken\CreateRefreshTokenDataTransformer;
use Tranquillity\Application\Service\FindRefreshTokenByToken\FindRefreshTokenByTokenDataTransformer;
use Tranquillity\Domain\Model\Auth\RefreshToken;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\RefreshToken as OAuthRefreshToken;

class RefreshTokenDataTransformer implements
    FindRefreshTokenByTokenDataTransformer,
    CreateRefreshTokenDataTransformer
{
    private ?OAuthRefreshToken $data = null;

    public function write(RefreshToken $entity): void
    {
        $this->data = new OAuthRefreshToken();
    }

    public function read(): OAuthRefreshToken
    {
        return $this->data;
    }

    public function writeError(string $code, string $detail, string $source = '', string $field = '', array $meta = []): void
    {
        $this->data = null;
    }

    public function writeValidationError(Notification $notification): void
    {
        $this->data = null;
    }
}
