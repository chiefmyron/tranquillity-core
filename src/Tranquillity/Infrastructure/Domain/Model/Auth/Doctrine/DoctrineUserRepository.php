<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Model\Auth\UserId;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Tranquillity\Domain\Model\Auth\UserCollection;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    public function nextIdentity(): UserId
    {
        return UserId::create();
    }

    public function add(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    public function update(User $user): void
    {
        $this->getEntityManager()->persist($user);
    }

    public function list(array $filterConditions, array $sortConditions, int $pageNumber, int $pageSize): UserCollection
    {
        // Build query to retrieve list of people
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')->from(User::class, 'p');

        // Build query criteria from supplied conditions
        //$criteria = Criteria::create();

        // Generate finalised query
        $query = $queryBuilder->getQuery();

        // Paginate results if required
        if ($pageSize > 0 && $pageNumber > 0) {
            $offset = $pageSize * ($pageNumber - 1);
            $query->setFirstResult($offset);
            $query->setMaxResults($pageSize);
            $paginator = new Paginator($query, true);

            // Generate paginated result collection
            return new UserCollection($paginator, $paginator->count(), $pageNumber, $pageSize);
        }

        // Execute query and return result set
        $result = $query->getResult();
        $totalRecordCount = count($result);
        return new UserCollection($result, $totalRecordCount, $pageNumber, $pageSize);
    }

    public function findById(UserId $id): ?User
    {
        return $this->getEntityManager()->find(User::class, $id);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }
}
