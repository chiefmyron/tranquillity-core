<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\Auth;

use Tranquillity\Application\DataTransformer\GenericDataTransformer;
use Tranquillity\Domain\Model\Auth\Client;

interface ClientDataTransformer extends GenericDataTransformer
{
    /**
     * @param Client $entity
     * @return void
     */
    public function write(Client $entity);

    /**
     * @return mixed
     */
    public function read();
}
