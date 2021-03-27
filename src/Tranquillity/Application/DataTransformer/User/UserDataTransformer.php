<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\User;

use Tranquillity\Application\DataTransformer\GenericDataTransformer;
use Tranquillity\Domain\Model\User\User;

interface UserDataTransformer extends GenericDataTransformer
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
