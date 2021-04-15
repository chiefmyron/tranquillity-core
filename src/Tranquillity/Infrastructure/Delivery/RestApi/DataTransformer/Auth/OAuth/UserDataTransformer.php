<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameDataTransformer;
use Tranquillity\Application\Service\ViewUser\ViewUserDataTransformer;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\User as OAuthUser;

class UserDataTransformer implements
    ViewUserDataTransformer,
    FindUserByUsernameDataTransformer
{
    private ?OAuthUser $data = null;

    public function write(User $entity): void
    {
        $this->data = new OAuthUser($entity->username());
    }

    public function read()
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
