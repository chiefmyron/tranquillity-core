<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\CreateUser;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\User;

interface CreateUserDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param User $entity
     * @return void
     */
    public function write(User $entity);

    /**
     * @return mixed
     */
    public function read();
}
