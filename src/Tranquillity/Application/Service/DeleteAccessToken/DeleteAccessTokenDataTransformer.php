<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteAccessToken;

use Tranquillity\Application\Service\ApplicationDataTransformer;

interface DeleteAccessTokenDataTransformer extends ApplicationDataTransformer
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
