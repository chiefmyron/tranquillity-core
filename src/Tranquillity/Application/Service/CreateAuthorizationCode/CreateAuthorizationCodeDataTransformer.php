<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateAuthorizationCode;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\AuthorizationCode;

interface CreateAuthorizationCodeDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param AuthorizationCode $entity
     * @return void
     */
    public function write(AuthorizationCode $entity);

    /**
     * @return mixed
     */
    public function read();
}
