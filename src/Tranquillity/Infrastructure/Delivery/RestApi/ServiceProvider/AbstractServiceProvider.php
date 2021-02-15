<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use DI\ContainerBuilder;

abstract class AbstractServiceProvider
{
    /**
     * Registers one or more services with the application container
     *
     * @param ContainerBuilder $containerBuilder DI container builder
     * @return void
     */
    abstract public function register(ContainerBuilder $containerBuilder);
}
