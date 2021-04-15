<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Authentication\OAuth\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessToken implements AccessTokenEntityInterface
{
    // Use package traits for convenience
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    /*public function __construct(
        string $id,
        ClientEntityInterface $client,
        array $scopes,
        $userIdentifier = null
    ) {
        $this->setIdentifier($id);
        $this->setClient($client);
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
        $this->setUserIdentifier($userIdentifier);
    }*/
}
