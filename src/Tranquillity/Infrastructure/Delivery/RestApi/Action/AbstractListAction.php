<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Enum\FilterOperatorEnum;
use Tranquillity\Infrastructure\Enum\SortDirectionEnum;

abstract class AbstractListAction extends AbstractAction
{

    /**
     * Parse filtering parameters from query string
     *
     * @see https://jsonapi.org/format/#fetching-filtering
     * @see https://jsonapi.org/recommendations/#filtering
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getFilterParameters(ServerRequestInterface $request): array
    {
        // Get array of query string parameters from request
        $queryStringParams = $request->getQueryParams();
        $filterParam = $queryStringParams['filter'] ?? array();

        // Get filtering parameters
        $filters = [];
        foreach ($filterParam as $field => $value) {
            // Check to see if the value is prefixed with an operator
            $valueParams = explode(':', $value);
            if (count($valueParams) == 1) {
                // Check to see if the value parameter is a NULL operator, or just a value
                if ($valueParams[0] == FilterOperatorEnum::IS_NULL || $valueParams[0] == FilterOperatorEnum::IS_NOT_NULL) {
                    // NULL and NOT NULL operators will not have a value parameter
                    $filters[] = [$field, $valueParams[0]];
                } else {
                    // Not prefixed with an operator - assume equality
                    $filters[] = [$field, FilterOperatorEnum::EQUALS, $valueParams[0]];
                }
            } else {
                // Use the prefixed operator
                $filters[] = [$field, $valueParams[0], $valueParams[1]];
            }
        }

        return $filters;
    }


    /**
     * Parse sort order parameters from query string
     *
     * @see https://jsonapi.org/format/#fetching-sorting
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getSortParameters(ServerRequestInterface $request): array
    {
        // Get array of query string parameters from request
        $queryStringParams = $request->getQueryParams();
        $sortParam = $queryStringParams['sort'] ?? '';

        // Get sorting parameters
        $sorting = [];
        $sortParams = explode(',', $sortParam);
        foreach ($sortParams as $sortItem) {
            // If the item starts with a minus character, it indicates descending sort order for that field
            if (mb_substr($sortItem, 0, 1) == '-') {
                $sortItem = mb_substr($sortItem, 1);
                $sorting[] = [$sortItem, SortDirectionEnum::DESCENDING];
            } elseif (mb_strlen($sortItem) > 0) {
                $sorting[] = [$sortItem, SortDirectionEnum::ASCENDING];
            }
        }

        return $sortParams;
    }

    /**
     * Parse page number parameter from query string
     *
     * @param ServerRequestInterface $request
     * @return int
     */
    protected function getPageNumber(ServerRequestInterface $request): int
    {
        // Get array of query string parameters from request
        $queryStringParams = $request->getQueryParams();
        $pageParam = $queryStringParams['page'] ?? array();

        $pageNumber = $pageParam['number'] ?? 0;
        return (int)$pageNumber;
    }

    /**
     * Parse page size parameter from query string
     *
     * @param ServerRequestInterface $request
     * @return int
     */
    protected function getPageSize(ServerRequestInterface $request): int
    {
        // Get array of query string parameters from request
        $queryStringParams = $request->getQueryParams();
        $pageParam = $queryStringParams['page'] ?? array();

        $pageSize = $pageParam['size'] ?? 0;
        return (int)$pageSize;
    }
}
