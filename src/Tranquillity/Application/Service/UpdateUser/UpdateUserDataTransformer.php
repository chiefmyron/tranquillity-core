<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\UpdateUser;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\User;

interface UpdateUserDataTransformer extends ApplicationDataTransformer
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
