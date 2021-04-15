<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class Client implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;

    public function __construct(string $id, string $name, string $redirectUri, bool $isConfidential = false)
    {
        $this->setIdentifier($id);
        $this->name = $name;
        $this->redirectUri = $redirectUri;
        $this->isConfidential = $isConfidential;
    }
}
