<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\User;

use Tranquillity\Domain\Model\User\User;

interface UserDataTransformer
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
