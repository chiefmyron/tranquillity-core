<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\Auth;

use Tranquillity\Domain\Model\Auth\UserCollection;

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
