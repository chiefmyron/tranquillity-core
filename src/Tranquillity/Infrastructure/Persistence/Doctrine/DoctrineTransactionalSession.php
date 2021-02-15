<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManager;
use Tranquillity\Application\Service\TransactionalSession;

class DoctrineTransactionalSession implements TransactionalSession
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function executeTransaction(callable $operation)
    {
        return $this->em->transactional($operation);
    }
}
