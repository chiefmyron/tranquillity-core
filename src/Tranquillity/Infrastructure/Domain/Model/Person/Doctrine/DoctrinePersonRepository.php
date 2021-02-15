<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Person\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Doctrine\ORM\EntityRepository;

class DoctrinePersonRepository extends EntityRepository implements PersonRepository
{
    public function nextIdentity(): PersonId
    {
        return PersonId::create();
    }

    public function add(Person $person): void
    {
        $this->getEntityManager()->persist($person);
    }

    public function remove(Person $person): void
    {
        $this->getEntityManager()->remove($person);
    }

    public function update(Person $person): void
    {
        $this->getEntityManager()->persist($person);
    }

    public function list(array $filterConditions, array $sortConditions, int $pageNumber, int $pageSize): array
    {
        // Build query to retrieve list of people
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('p')->from(Person::class, 'p');

        // Build query criteria from supplied conditions
        //$criteria = Criteria::create();

        // Generate finalised query
        $query = $queryBuilder->getQuery();

        // Paginate results if required
        if ($pageSize > 0 && $pageNumber > 0) {
            $offset = $pageSize * ($pageNumber - 1);
            $query->setFirstResult($offset);
            $query->setMaxResults($pageSize);
        }

        // Execute query and return result set
        return $query->getResult();
    }

    public function findById(PersonId $id): Person
    {
        return $this->getEntityManager()->find(Person::class, $id);
    }
}
