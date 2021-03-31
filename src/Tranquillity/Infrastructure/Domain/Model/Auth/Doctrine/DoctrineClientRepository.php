<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Tranquillity\Domain\Model\Auth\Client;
use Tranquillity\Domain\Model\Auth\ClientId;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{
    public function nextIdentity(): ClientId
    {
        return ClientId::create();
    }

    public function add(Client $client): void
    {
        $this->getEntityManager()->persist($client);
    }

    public function remove(Client $client): void
    {
        $this->getEntityManager()->remove($client);
    }

    public function update(Client $client): void
    {
        $this->getEntityManager()->persist($client);
    }

    public function findById(ClientId $id): ?Client
    {
        return $this->getEntityManager()->find(Client::class, $id);
    }

    public function findByName(string $name): ?Client
    {
        return $this->findOneBy(['name' => $name]);
    }
}
