<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;

abstract class ResourceCollectionDataTransformer
{
    protected ServerRequestInterface $request;
    protected RouteParserInterface $routeParser;
    protected ?RouteInterface $route = null;

    protected array $data = [];
    protected array $included = [];
    protected array $links = [];
    protected array $meta = [];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;

        // Extract route details from the request
        $routeContext = RouteContext::fromRequest($request);
        $this->routeParser = $routeContext->getRouteParser();
        $this->route = $routeContext->getRoute();
    }

    public function read(): array
    {
        // Build return array
        $result = [];
        if (count($this->links) > 0) {
            $result['links'] = $this->links;
        }
        if (count($this->data) > 0) {
            $result['data'] = $this->data;
        }
        if (count($this->included) > 0) {
            $result['included'] = $this->included;
        }
        if (count($this->meta) > 0) {
            $result['meta'] = $this->meta;
        }
        $result['jsonapi'] = ['version' => '1.0'];
        return $result;
    }

    protected function getRouteName(): string
    {
        if (null === $this->route) {
            return '';
        }

        return $this->route->getName();
    }

    protected function getRouteArguments(): array
    {
        if (is_null($this->route) == true) {
            return [];
        }

        return $this->route->getArguments();
    }

    protected function getRouteArgument(string $name, ?string $default = null): mixed
    {
        if (is_null($this->route) == true) {
            return $default;
        }

        return $this->route->getArgument($name, $default);
    }
}
