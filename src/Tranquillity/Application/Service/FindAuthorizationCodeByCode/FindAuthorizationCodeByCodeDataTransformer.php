<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindAuthorizationCodeByCode;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\AuthorizationCode;

interface FindAuthorizationCodeByCodeDataTransformer extends ApplicationDataTransformer
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
