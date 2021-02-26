<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Enum\FilterOperatorEnum;
use Tranquillity\Infrastructure\Enum\SortDirectionEnum;

abstract class AbstractAction
{

    /**
     * Parse sparse fieldset definitions from query string
     *
     * @see https://jsonapi.org/format/#fetching-sparse-fieldsets
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getSparseFieldset(ServerRequestInterface $request): array
    {
        // Get array of sparse fieldsets, defined per resource type
        $queryStringParams = $request->getQueryParams();
        $fieldsParam = $queryStringParams['fields'] ?? [];

        // Individual fields for a resource type are comma separated
        $fieldset = [];
        foreach ($fieldsParam as $resourceType => $fieldNames) {
            // Add fields for the resource type
            $fieldset[$resourceType] = explode(",", $fieldNames);
        }
        return $fieldset;
    }

    /**
     * Parse inclusion of related resources from query string
     *
     * @see https://jsonapi.org/format/#fetching-includes
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function getIncludedResources(ServerRequestInterface $request): array
    {
        // Get the list of includes
        $queryStringParams = $request->getQueryParams();
        $includeParam = $queryStringParams['include'] ?? '';
        if ($includeParam == '') {
            return [];
        }

        return explode(",", $includeParam);
    }
}
