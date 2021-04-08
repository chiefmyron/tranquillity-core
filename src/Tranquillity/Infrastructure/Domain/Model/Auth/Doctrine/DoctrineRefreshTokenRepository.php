<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Tranquillity\Domain\Model\Auth\RefreshToken;
use Tranquillity\Domain\Model\Auth\RefreshTokenId;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineRefreshTokenRepository extends EntityRepository implements RefreshTokenRepository
{
    public function nextIdentity(): RefreshTokenId
    {
        return RefreshTokenId::create();
    }

    public function add(RefreshToken $refreshToken): void
    {
        $this->getEntityManager()->persist($refreshToken);
    }

    public function remove(RefreshToken $refreshToken): void
    {
        $this->getEntityManager()->remove($refreshToken);
    }

    public function update(RefreshToken $refreshToken): void
    {
        $this->getEntityManager()->persist($refreshToken);
    }

    public function findById(RefreshTokenId $id): ?RefreshToken
    {
        return $this->getEntityManager()->find(RefreshToken::class, $id);
    }

    public function findByToken(string $token): ?RefreshToken
    {
        return $this->findOneBy(['token' => $token]);
    }
}
