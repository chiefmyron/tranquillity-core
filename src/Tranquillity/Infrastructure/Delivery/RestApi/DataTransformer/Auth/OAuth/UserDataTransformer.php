<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameDataTransformer;
use Tranquillity\Application\Service\ViewUser\ViewUserDataTransformer;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Validation\Notification;

class UserDataTransformer implements
    ViewUserDataTransformer,
    FindUserByUsernameDataTransformer
{
    private array $data = [];

    public function write(User $entity): void
    {
        $this->data = [
            'user_id' => $entity->username(),
            'scope' => ''
        ];
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
