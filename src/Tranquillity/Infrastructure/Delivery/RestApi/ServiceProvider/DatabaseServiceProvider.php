<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use Exception;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\DBAL\Types\Type;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Tranquillity\Infrastructure\Domain\Model\Person\Doctrine\DoctrinePersonId;
use Tranquillity\Infrastructure\Persistence\Doctrine\TablePrefixExtension;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * Registers the service with the application container
     *
     * @return void
     */
    public function register(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinitions([
            EntityManagerInterface::class => function (ContainerInterface $c) {
                // Get connection and options from config
                $config = $c->get('config');
                $options = $config->get('database.options', []);
                $connection = $config->get('database.connection', []);

                // Create Doctrine configuration
                $config = Setup::createConfiguration(
                    $options['auto_generate_proxies'],
                    $options['proxy_dir'],
                    $options['cache']
                );

                // Create Doctrine configuration
                $driver = new XmlDriver($options['mappings_dir']);
                $config->setMetadataDriverImpl($driver);

                // Add event listeners
                $eventManager = new EventManager();
                $tablePrefixEventManager = new TablePrefixExtension($options['table_prefix']);
                $eventManager->addEventListener(Events::loadClassMetadata, $tablePrefixEventManager);

                // Create Doctrine entity manager
                try {
                    // Register entity ID types
                    Type::addType('PersonId', DoctrinePersonId::class);

                    // Create entity manager
                    $entityManager = EntityManager::create($connection, $config, $eventManager);

                    // Register UUID data type
                    Type::addType(UuidBinaryOrderedTimeType::NAME, UuidBinaryOrderedTimeType::class);
                    $entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping(UuidBinaryOrderedTimeType::NAME, 'binary');

                    return $entityManager;
                } catch (Exception $e) {
                    throw new Exception("An error occurred while trying to connect to the database: " . $e->getMessage());
                }
            },

            Connection::class => function (ContainerInterface $c): Connection {
                $entityManager = $c->get(EntityManagerInterface::class);
                return $entityManager->getConnection();
            },
        ]);
    }
}
