<?php

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponse;

class Application
{
    /**
     * Bootstrap Slim application
     *
     * @param string $basepath Path to base application directory (used to load config and resources)
     * @return App
     */
    public static function bootstrap(string $basepath)
    {
        // Load application configuration
        $config = ConfigLoader::load($basepath);

        // Build dependency injection container
        $containerBuilder = new ContainerBuilder();
        if ($config->has('app.di_compliation_path')) {
            $containerBuilder->enableCompilation($config->get('app.di_compilation_path'));
        }
        $containerBuilder->addDefinitions(['config' => $config]);
        $container = DependencyLoader::load($containerBuilder);

        // Initialise application
        AppFactory::setContainer($container);
        $app = AppFactory::create();

        // Assign matched route arguments to Request attributes for PSR-15 handlers
        $app->getRouteCollector()->setDefaultInvocationStrategy(new RequestResponse());

        // Register middleware
        MiddlewareLoader::load($app);

        // Register routes
        RouteLoader::load($app);

        // Return bootstrapped application
        return $app;
    }
}
