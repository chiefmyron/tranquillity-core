<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi;

use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Infrastructure\Output\JsonApi\Document\AbstractDocument;

abstract class AbstractDataTransformer
{
    protected RouteCollectorInterface $routeCollector;
    protected AbstractDocument $document;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function read()
    {
        return $this->document;
    }
}
