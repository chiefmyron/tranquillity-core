<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteRefreshToken;

use Tranquillity\Application\Service\ApplicationDataTransformer;

interface DeleteRefreshTokenDataTransformer extends ApplicationDataTransformer
{
    /**
     * @return void
     */
    public function write();

    /**
     * @return mixed
     */
    public function read();
}
