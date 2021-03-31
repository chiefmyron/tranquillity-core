<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\Auth;

use Tranquillity\Application\DataTransformer\GenericDataTransformer;
use Tranquillity\Domain\Model\Auth\AccessToken;

interface AccessTokenDataTransformer extends GenericDataTransformer
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
