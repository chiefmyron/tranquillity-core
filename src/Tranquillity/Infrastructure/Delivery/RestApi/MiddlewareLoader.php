<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi;

use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Error\Renderers\PlainTextErrorRenderer;
use Slim\Middleware\ContentLengthMiddleware;
use Tranquillity\Infrastructure\Delivery\RestApi\Error\RestApiErrorHandler;
use Tranquillity\Infrastructure\Delivery\RestApi\Error\Renderer\JsonApiErrorRenderer;
use Tranquillity\Infrastructure\Delivery\RestApi\Middleware\EventSubscriberMiddleware;
use Tranquillity\Infrastructure\Delivery\RestApi\Middleware\JsonApiRequestValidationMiddleware;
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
        $config = $container->get('config');

        // Register middlewares
        $app->add(ProfilerMiddleware::class);
        $app->add(EventSubscriberMiddleware::class);
        //$app->add(JsonApiRequestValidationMiddleware::class);
        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        //$app->add(ContentLengthMiddleware::class);
        $errorMiddleware = $app->addErrorMiddleware($config->get('app.debug'), true, true, $logger);

        // Set up custom error handler
        $responseErrorRenderer = new JsonApiErrorRenderer();
        $logErrorRenderer = new PlainTextErrorRenderer();
        $errorHandler = new RestApiErrorHandler($app->getResponseFactory(), $logger, $responseErrorRenderer, $logErrorRenderer);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);
    }
}
