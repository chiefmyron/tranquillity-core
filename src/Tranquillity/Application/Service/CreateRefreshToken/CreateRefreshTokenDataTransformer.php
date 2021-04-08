<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateRefreshToken;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\RefreshToken;

interface CreateRefreshTokenDataTransformer extends ApplicationDataTransformer
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
