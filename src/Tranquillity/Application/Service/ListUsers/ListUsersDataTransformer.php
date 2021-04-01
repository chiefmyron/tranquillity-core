<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\ListUsers;

use Tranquillity\Application\Service\ApplicationDataTransformer;
use Tranquillity\Domain\Model\Auth\UserCollection;

interface ListUsersDataTransformer extends ApplicationDataTransformer
{
    /**
     * @param UserCollection $collection
     * @return void
     */
    public function write(UserCollection $collection);

    /**
     * @return mixed
     */
    public function read();
}
