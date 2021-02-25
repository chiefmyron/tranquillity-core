<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Person\Doctrine;

use Doctrine\Common\Collections\Criteria;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Tranquillity\Domain\Model\Person\PersonCollection;

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

    public function list(array $filterConditions, array $sortConditions, int $pageNumber, int $pageSize): PersonCollection
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
            $paginator = new Paginator($query, true);

            // Generate paginated result collection
            return new PersonCollection($paginator, $paginator->count(), $pageNumber, $pageSize);
        }

        // Execute query and return result set
        $result = $query->getResult();
        $totalRecordCount = count($result);
        return new PersonCollection($result, $totalRecordCount, $pageNumber, $pageSize);
    }

    public function findById(PersonId $id): ?Person
    {
        return $this->getEntityManager()->find(Person::class, $id);
    }
}
