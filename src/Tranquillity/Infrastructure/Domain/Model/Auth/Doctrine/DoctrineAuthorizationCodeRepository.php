<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Tranquillity\Domain\Model\Auth\AuthorizationCode;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeId;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Doctrine\ORM\EntityRepository;

class DoctrineAuthorizationCodeRepository extends EntityRepository implements AuthorizationCodeRepository
{
    public function nextIdentity(): AuthorizationCodeId
    {
        return AuthorizationCodeId::create();
    }

    public function add(AuthorizationCode $authorizationCode): void
    {
        $this->getEntityManager()->persist($authorizationCode);
    }

    public function remove(AuthorizationCode $authorizationCode): void
    {
        $this->getEntityManager()->remove($authorizationCode);
    }

    public function update(AuthorizationCode $authorizationCode): void
    {
        $this->getEntityManager()->persist($authorizationCode);
    }

    public function findById(AuthorizationCodeId $id): ?AuthorizationCode
    {
        return $this->getEntityManager()->find(AuthorizationCode::class, $id);
    }

    public function findByCode(string $code): ?AuthorizationCode
    {
        return $this->findOneBy(['code' => $code]);
    }
}
