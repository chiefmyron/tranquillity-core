<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\CreateAccessToken\CreateAccessTokenDataTransformer;
use Tranquillity\Application\Service\FindAccessTokenByToken\FindAccessTokenByTokenDataTransformer;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\AccessToken as OAuthAccessToken;

class AccessTokenDataTransformer implements
    FindAccessTokenByTokenDataTransformer,
    CreateAccessTokenDataTransformer
{
    private ?OAuthAccessToken $data = null;

    public function write(AccessToken $entity): void
    {
        $this->data = new OAuthAccessToken();
    }

    public function read(): ?OAuthAccessToken
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
