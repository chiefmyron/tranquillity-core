<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use DI\Container;
use DI\ContainerBuilder;
use Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider\ApplicationServiceProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider\LoggerServiceProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider\DatabaseServiceProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider\AuthenticationServiceProvider;
use Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider\ProfilerServiceProvider;

class DependencyLoader
{
    /**
     * Add custom dependency definitions to DI container
     *
     * @param ContainerBuilder $containerBuilder
     * @return Container
     */
    public static function load(ContainerBuilder $containerBuilder)
    {
        // Use application service providers to register additional dependencies
        $serviceProviders = [
            LoggerServiceProvider::class,
            DatabaseServiceProvider::class,
            ApplicationServiceProvider::class,
            AuthenticationServiceProvider::class,
            ProfilerServiceProvider::class
        ];
        foreach ($serviceProviders as $providerClassname) {
            $provider = new $providerClassname();
            $provider->register($containerBuilder);
        }

        // Build finalised container
        return $containerBuilder->build();
    }
}
