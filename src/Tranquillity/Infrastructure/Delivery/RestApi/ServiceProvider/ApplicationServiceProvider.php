<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Domain\Event\StoredEvent;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;

/**
 * Defines all of the implementation-specific infrastructure dependencies
 * to the Application layer
 */
class ApplicationServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinitions([
            // Register repositories
            PersonRepository::class => function (ContainerInterface $c): PersonRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(Person::class);
            },


            // Register persistence transaction handler
            TransactionalSession::class => function (ContainerInterface $c): TransactionalSession {
                $em = $c->get(EntityManagerInterface::class);
                return new DoctrineTransactionalSession($em);
            },

            // Register domain event subscribers
            DomainEventStore::class => function (ContainerInterface $c): DomainEventStore {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(StoredEvent::class);
            },
            DomainEventPublisher::class => function (ContainerInterface $c): DomainEventPublisher {
                return DomainEventPublisher::instance();
            }
        ]);
    }
}
