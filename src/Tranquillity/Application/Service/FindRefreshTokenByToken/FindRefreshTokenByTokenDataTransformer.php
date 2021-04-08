<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindRefreshTokenByToken;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\RefreshToken;

interface FindRefreshTokenByTokenDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param RefreshToken $entity
     * @return void
     */
    public function write(RefreshToken $entity);

    /**
     * @return mixed
     */
    public function read();
}
