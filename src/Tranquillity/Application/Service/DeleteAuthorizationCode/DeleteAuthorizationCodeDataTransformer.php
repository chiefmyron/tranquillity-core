<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\DeleteAuthorizationCode;

use Tranquillity\Application\Service\ApplicationDataTransformer;

interface DeleteAuthorizationCodeDataTransformer extends ApplicationDataTransformer
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
