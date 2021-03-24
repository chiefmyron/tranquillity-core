<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\User;

use Tranquillity\Domain\Model\User\UserCollection;

interface UserCollectionDataTransformer
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
