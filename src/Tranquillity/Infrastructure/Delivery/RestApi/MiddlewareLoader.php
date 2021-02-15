<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Middleware\ContentLengthMiddleware;
use Tranquillity\Infrastructure\Delivery\RestApi\Middleware\ProfilerMiddleware;

class MiddlewareLoader
{
    /**
     * Loads PSR-15 compatible middlewares to be executed throughout the
     * request / response cycle
     *
     * @param App $app
     * @return void
     */
    public static function load(App $app)
    {
        // Get logger from container
        $container = $app->getContainer();
        if (is_null($container) == true) {
            return;
        }
        $logger = $container->get(LoggerInterface::class);

        // Register middlewares
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $app->add(ContentLengthMiddleware::class);
        $app->add(ProfilerMiddleware::class);
        $errorMiddleware = $app->addErrorMiddleware(true, true, true, $logger);

        // Add custom error renderer
        /*$errorHandler = $errorMiddleware->getDefaultErrorHandler();
        if (is_object($errorHandler) === true) {
            $errorHandler->registerErrorRenderer('application/vnd.api+json', ErrorRenderer::class);
            $errorHandler->forceContentType('application/vnd.api+json');
        }*/
    }
}
