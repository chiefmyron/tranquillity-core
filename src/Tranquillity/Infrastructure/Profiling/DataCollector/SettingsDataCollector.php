<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tranquillity\Infrastructure\Config\Config;

class SettingsDataCollector extends AbstractDataCollector implements DataCollectorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'settings';
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $this->data = $this->config->all();
    }

    /**
     * {@inheritDoc}
     */
    public function reset()
    {
        $this->data = [];
    }
}
