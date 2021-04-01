<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindClientByName;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\Client;

interface FindClientByNameDataTransformer extends ApplicationDataTransformer
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
