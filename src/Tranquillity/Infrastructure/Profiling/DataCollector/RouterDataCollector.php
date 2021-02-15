<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext;

class RouterDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'router';
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        if (is_null($route) === false) {
            $callable = $route->getCallable();
            $this->data = [
                'name' => $route->getName(),
                'pattern' => $route->getPattern(),
                'arguments' => $route->getArguments(),
                'callable' => $this->getCallableName($route->getCallable())
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function lateCollect()
    {
        // Transform collected data into an array that can be serialised
        $this->data = $this->cloneVar($this->data);
    }

    /**
     * Get a string representation of a callable
     *
     * @param string|array|callable $callable
     * @return string
     */
    private function getCallableName($callable)
    {
        if (is_string($callable)) {
            return trim($callable);
        } else if (is_array($callable)) {
            if (is_object($callable[0])) {
                return sprintf("%s::%s", get_class($callable[0]), trim($callable[1]));
            } else {
                return sprintf("%s::%s", trim($callable[0]), trim($callable[1]));
            }
        } else if ($callable instanceof \Closure) {
            return 'closure';
        } else {
            return 'unknown';
        }
    }
}
