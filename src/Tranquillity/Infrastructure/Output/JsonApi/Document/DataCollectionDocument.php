<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\Document;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\AbstractResourceCollection;

class DataCollectionDocument extends AbstractDocument
{
    protected AbstractResourceCollection $collection;

    public function __construct(AbstractResourceCollection $collection)
    {
        $this->collection = $collection;
    }

    public function render(ServerRequestInterface $request, bool $includeSelfLink = true, bool $includePaginationLinks = true): array
    {
        // Include 'self' as a top-level link
        if ($includeSelfLink == true) {
            $this->addLink('self', (string)$request->getUri());
        }

        // Include pagination as top-level links
        $totalRecordCount = $this->collection->totalRecordCount();
        $pageNumber = $this->collection->pageNumber();
        $pageSize = $this->collection->pageSize();
        if ($includePaginationLinks == true && $pageNumber > 0 && $pageSize > 0) {
            // Calculate pagination limits
            $lastPageNumber = ceil($totalRecordCount / $pageSize);

            // Get sparse fieldset and included resources from request
            $queryStringParams = $request->getQueryParams();

            $routeContext = RouteContext::fromRequest($request);
            $routeParser = $routeContext->getRouteParser();
            $route = $routeContext->getRoute();
            $uri = $request->getUri();

            // 'First' link
            $queryStringParams['page[number]'] = 1;
            $queryStringParams['page[size]'] = $pageSize;
            $this->addLink('first', $routeParser->fullUrlFor($uri, $route->getName(), $route->getArguments(), $queryStringParams));

            // 'Last' link
            $queryStringParams['page[number]'] = $lastPageNumber;
            $queryStringParams['page[size]'] = $pageSize;
            $this->addLink('last', $routeParser->fullUrlFor($uri, $route->getName(), $route->getArguments(), $queryStringParams));

            // 'Previous' link
            if ($pageNumber > 1) {
                $queryStringParams['page[number]'] = ($pageNumber - 1);
                $queryStringParams['page[size]'] = $pageSize;
                $this->addLink('prev', $routeParser->fullUrlFor($uri, $route->getName(), $route->getArguments(), $queryStringParams));
            } else {
                $this->addLink('prev', null);
            }

            // 'Next' link
            if ($pageNumber < $lastPageNumber) {
                $queryStringParams['page[number]'] = ($pageNumber + 1);
                $queryStringParams['page[size]'] = $pageSize;
                $this->addLink('next', $routeParser->fullUrlFor($uri, $route->getName(), $route->getArguments(), $queryStringParams));
            } else {
                $this->addLink('next', null);
            }

            // Add total record count as metadata
            $this->addMeta('totalRecordCount', $totalRecordCount);
        }

        // Get common members
        $result = parent::render($request);

        // Return completed document body
        $result['data'] = $this->collection->render();
        return $result;
    }
}
