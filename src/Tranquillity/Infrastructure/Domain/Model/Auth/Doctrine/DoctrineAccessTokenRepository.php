<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Model\Auth\AccessTokenId;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineAccessTokenRepository extends EntityRepository implements AccessTokenRepository
{
    public function nextIdentity(): AccessTokenId
    {
        return AccessTokenId::create();
    }

    public function add(AccessToken $accessToken): void
    {
        $this->getEntityManager()->persist($accessToken);
    }

    public function remove(AccessToken $accessToken): void
    {
        $this->getEntityManager()->remove($accessToken);
    }

    public function update(AccessToken $accessToken): void
    {
        $this->getEntityManager()->persist($accessToken);
    }

    public function findById(AccessTokenId $id): ?AccessToken
    {
        return $this->getEntityManager()->find(AccessToken::class, $id);
    }

    public function findByToken(string $token): ?AccessToken
    {
        return $this->findOneBy(['token' => $token]);
    }
}
