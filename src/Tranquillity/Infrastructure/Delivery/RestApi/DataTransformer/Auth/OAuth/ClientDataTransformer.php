<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\FindClientByName\FindClientByNameDataTransformer;
use Tranquillity\Application\Service\ViewClient\ViewClientDataTransformer;
use Tranquillity\Domain\Model\Auth\Client;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Infrastructure\Authentication\OAuth\Entity\Client as OAuthClient;

class ClientDataTransformer implements
    ViewClientDataTransformer,
    FindClientByNameDataTransformer
{
    private OAuthClient $data;

    public function write(Client $entity): void
    {
        $this->data = new OAuthClient(
            $entity->getIdValue(),
            $entity->name(),
            $entity->redirectUri(),
            true
        );
    }

    public function read(): OAuthClient
    {
        return $this->data;
    }

    public function writeError(string $code, string $detail, string $source = '', string $field = '', array $meta = []): void
    {
        throw new \Exception("Not implemented");
    }

    public function writeValidationError(Notification $notification): void
    {
        throw new \Exception("Not implemented");
    }
}
