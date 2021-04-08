<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\DeleteAuthorizationCode\DeleteAuthorizationCodeDataTransformer;
use Tranquillity\Application\Service\DeleteRefreshToken\DeleteRefreshTokenDataTransformer;
use Tranquillity\Domain\Validation\Notification;

class EmptyDataTransformer implements
    DeleteRefreshTokenDataTransformer,
    DeleteAuthorizationCodeDataTransformer
{
    private array $data = [];

    public function write(): void
    {
        $this->data = [];
    }

    public function read()
    {
        if (count($this->data) <= 0) {
            return false;
        }
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
