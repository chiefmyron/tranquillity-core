<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service;

interface TransactionalSession
{
    /**
     * @return mixed
     */
    public function executeTransaction(callable $operation);
}
