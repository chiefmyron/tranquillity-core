<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ViewClient;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\Client;

interface ViewClientDataTransformer extends ApplicationDataTransformer
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
