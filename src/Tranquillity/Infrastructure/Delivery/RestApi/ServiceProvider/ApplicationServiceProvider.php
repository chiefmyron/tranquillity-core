<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Application\Service\CreatePerson\CreatePersonDataTransformer;
use Tranquillity\Application\Service\CreateUser\CreateUserDataTransformer;
use Tranquillity\Application\Service\ListPeople\ListPeopleDataTransformer;
use Tranquillity\Application\Service\ListUsers\ListUsersDataTransformer;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Application\Service\UpdatePerson\UpdatePersonDataTransformer;
use Tranquillity\Application\Service\UpdateUser\UpdateUserDataTransformer;
use Tranquillity\Application\Service\ViewPerson\ViewPersonDataTransformer;
use Tranquillity\Application\Service\ViewUser\ViewUserDataTransformer;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Domain\Event\StoredEvent;
use Tranquillity\Domain\Model\Auth\AccessToken;
use Tranquillity\Domain\Model\Auth\AccessTokenRepository;
use Tranquillity\Domain\Model\Auth\AuthorizationCode;
use Tranquillity\Domain\Model\Auth\AuthorizationCodeRepository;
use Tranquillity\Domain\Model\Auth\Client;
use Tranquillity\Domain\Model\Auth\ClientRepository;
use Tranquillity\Domain\Model\Auth\RefreshToken;
use Tranquillity\Domain\Model\Auth\RefreshTokenRepository;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Domain\Model\Auth\UserRepository;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonRepository;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\JsonApi\UserCollectionDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\JsonApi\UserDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\PersonCollectionDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi\PersonDataTransformer;
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
            RefreshTokenRepository::class => function (ContainerInterface $c): RefreshTokenRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(RefreshToken::class);
            },
            ClientRepository::class => function (ContainerInterface $c): ClientRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(Client::class);
            },
            AuthorizationCodeRepository::class => function (ContainerInterface $c): AuthorizationCodeRepository {
                $em = $c->get(EntityManagerInterface::class);
                return $em->getRepository(AuthorizationCode::class);
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
            ViewPersonDataTransformer::class => DI\get(PersonDataTransformer::class),
            CreatePersonDataTransformer::class => DI\get(PersonDataTransformer::class),
            UpdatePersonDataTransformer::class => DI\get(PersonDataTransformer::class),
            ListPeopleDataTransformer::class => DI\get(PersonCollectionDataTransformer::class),

            ViewUserDataTransformer::class => DI\get(UserDataTransformer::class),
            CreateUserDataTransformer::class => DI\get(UserDataTransformer::class),
            UpdateUserDataTransformer::class => DI\get(UserDataTransformer::class),
            ListUsersDataTransformer::class => DI\get(UserCollectionDataTransformer::class),

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
