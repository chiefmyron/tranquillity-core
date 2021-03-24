<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Application\DataTransformer\Person\PersonCollectionDataTransformer;
use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Application\DataTransformer\User\UserCollectionDataTransformer;
use Tranquillity\Application\DataTransformer\User\UserDataTransformer;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Domain\Event\StoredEvent;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Domain\Model\User\User;
use Tranquillity\Domain\Model\User\UserRepository;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\ListPeopleDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\ViewPersonDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApi\ListUsersDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApi\ViewUserDataTransformer;
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
            UserRepository::class => function (ContainerInterface $c): UserRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(User::class);
            },

            // Register data transformers
            PersonDataTransformer::class => function (ContainerInterface $c): PersonDataTransformer {
                $routeCollector = $c->get(RouteCollectorInterface::class);
                return new ViewPersonDataTransformer($routeCollector);
            },
            PersonCollectionDataTransformer::class => function (ContainerInterface $c): PersonCollectionDataTransformer {
                $routeCollector = $c->get(RouteCollectorInterface::class);
                return new ListPeopleDataTransformer($routeCollector);
            },
            UserDataTransformer::class => function (ContainerInterface $c): UserDataTransformer {
                $routeCollector = $c->get(RouteCollectorInterface::class);
                return new ViewUserDataTransformer($routeCollector);
            },
            UserCollectionDataTransformer::class => function (ContainerInterface $c): UserCollectionDataTransformer {
                $routeCollector = $c->get(RouteCollectorInterface::class);
                return new ListUsersDataTransformer($routeCollector);
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
