<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\ServiceProvider;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;

class LoggerServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addDefinitions([
            // Register logging library
            LoggerInterface::class => function (ContainerInterface $c) {
                $config = $c->get('config')->get('logger');
                $logger = new Logger($config['name']);

                $processor = new UidProcessor();
                $logger->pushProcessor($processor);

                // Add handler based on logger type defined in config
                switch (strtolower($config['type'])) {
                    case 'file-rotating':
                        $path = $config['options']['path'] . DIRECTORY_SEPARATOR . $config['options']['filename'];
                        $formatter = new LineFormatter($config['options']['outputFormat'], $config['options']['dateFormat']);
                        $handler = new RotatingFileHandler($path, $config['options']['maxFiles'], $config['level']);
                        $handler->setFormatter($formatter);
                        $logger->pushHandler($handler);
                        break;
                    case 'file':
                    default:
                        $path = $config['options']['path'] . DIRECTORY_SEPARATOR . $config['options']['filename'];
                        $formatter = new LineFormatter($config['options']['outputFormat'], $config['options']['dateFormat']);
                        $handler = new StreamHandler($path, $config['level']);
                        $handler->setFormatter($formatter);
                        $logger->pushHandler($handler);
                        break;
                }

                return $logger;
            }
        ]);
    }
}
