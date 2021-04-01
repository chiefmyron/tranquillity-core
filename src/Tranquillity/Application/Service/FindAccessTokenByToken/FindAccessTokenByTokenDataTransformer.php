<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindAccessTokenByToken;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\AccessToken;

interface FindAccessTokenByTokenDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param AccessToken $entity
     * @return void
     */
    public function write(AccessToken $entity);

    /**
     * @return mixed
     */
    public function read();
}
