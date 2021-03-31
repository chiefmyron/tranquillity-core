<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Application\DataTransformer\Auth\UserCollectionDataTransformer;
use Tranquillity\Application\DataTransformer\Auth\UserDataTransformer;
use Tranquillity\Application\DataTransformer\Person\PersonCollectionDataTransformer;
use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Domain\Event\StoredEvent;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\Client;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\JsonApi\ListUsersDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\JsonApi\ViewUserDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\ListPeopleDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\ViewPersonDataTransformer;
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
            AccessTokenRepository::class => function (ContainerInterface $c): AccessTokenRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(AccessToken::class);
            },
            ClientRepository::class => function (ContainerInterface $c): ClientRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(Client::class);
            },
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
