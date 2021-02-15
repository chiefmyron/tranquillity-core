<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

// PSR standards interfaces
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Tranquillity\Infrastructure\Profiling\Profiler;
use Tranquillity\Infrastructure\Profiling\Storage\FileProfilerStorage;
use Tranquillity\Infrastructure\Profiling\DataCollector\DatabaseDataCollector;
use Tranquillity\Infrastructure\Profiling\DataCollector\EnvironmentDataCollector;
use Tranquillity\Infrastructure\Profiling\DataCollector\HttpDataCollector;
use Tranquillity\Infrastructure\Profiling\DataCollector\MemoryDataCollector;
use Tranquillity\Infrastructure\Profiling\DataCollector\RouterDataCollector;
use Tranquillity\Infrastructure\Profiling\DataCollector\SettingsDataCollector;

class ProfilerServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinitions([
            // Register Profiler library
            Profiler::class => function (ContainerInterface $c) {
                $config = $c->get('config');
                $profilerOptions = $config->get('profiler');
                $storageOptions = $config->get('profiler.storage_options');

                // Create profiler storage mechanism
                $storage = null;
                switch ($profilerOptions['storage_type']) {
                    case 'file':
                    default:
                        $path = $storageOptions['path'] ?? '../var/profiler';
                        $storage = new FileProfilerStorage($path, $storageOptions);
                        break;
                }

                // Create profiler engine
                $enabled = $profilerOptions['enabled'] ?? false;
                $logger = $c->get(LoggerInterface::class);
                $profiler = new Profiler($storage, $logger, $enabled);

                // Add default data collectors
                $profiler->addDataCollector(new EnvironmentDataCollector());
                $profiler->addDataCollector(new MemoryDataCollector());
                $profiler->addDataCollector(new HttpDataCollector());
                $profiler->addDataCollector(new RouterDataCollector());
                $profiler->addDataCollector(new SettingsDataCollector($config));

                // Add data collector for database connection
                $connection = $c->get(Connection::class);
                $profiler->addDataCollector(new DatabaseDataCollector($connection));

                // Return the profiler engine
                return $profiler;
            }
        ]);
    }
}
