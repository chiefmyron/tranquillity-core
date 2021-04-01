<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\OAuth;

use Tranquillity\Application\Service\FindClientByName\FindClientByNameDataTransformer;
use Tranquillity\Application\Service\ViewClient\ViewClientDataTransformer;
use Tranquillity\Domain\Model\Auth\Client;
use Tranquillity\Domain\Validation\Notification;

class ClientDataTransformer implements
    ViewClientDataTransformer,
    FindClientByNameDataTransformer
{
    private array $data = [];

    public function write(Client $entity): void
    {
        $this->data = [
            'id' => $entity->getIdValue(),
            'name' => $entity->name(),
            'secret' => $entity->secret(),
            'redirectUri' => $entity->redirectUri()
        ];
    }

    public function read(): array
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
