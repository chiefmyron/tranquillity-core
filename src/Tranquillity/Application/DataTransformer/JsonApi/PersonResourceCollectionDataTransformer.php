<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\JsonApi;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Slim\Routing\RouteContext;
use Tranquillity\Application\DataTransformer\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonCollection;

class PersonResourceCollectionDataTransformer extends AbstractResourceCollectionDataTransformer implements PersonCollectionDataTransformer
{
    /**
     * @param PersonCollection $personCollection
     * @param array If provided, applies sparse fieldset rules
     * @param array If provided, determines which related resources are included in a compound document
     * @return void
     */
    public function write(PersonCollection $personCollection, array $fields = [], array $relatedResources = [])
    {
        // Create a data transformer for the entity
        $dataTransformer = new PersonResourceObjectDataTransformer($this->request);

        // Generate resource objects for each element of the collection
        $collection = $personCollection->collection();
        foreach ($collection as $person) {
            $dataTransformer->write($person, $fields, $relatedResources);
            $personResource = $dataTransformer->read();
            $this->data[] = $personResource['data'];
        }

        $this->links = $this->writeLinks($personCollection);
    }

    private function writeLinks(PersonCollection $personCollection): array
    {
        // Create links for pagination
        $links = [];

        // Get pagination details for the collection
        $totalRecordCount = $personCollection->totalRecordCount();
        $pageNumber = $personCollection->pageNumber();
        $pageSize = $personCollection->pageSize();

        // If there are no pagination details, response cannot be paginated
        if ($pageNumber == 0 || $pageSize == 0) {
            return $links;
        }

        // Get route parser
        $uri = $this->request->getUri();
        $routeParser = RouteContext::fromRequest($this->request)->getRouteParser();

        // Calculate pagination limits
        $lastPageNumber = ceil($totalRecordCount / $pageSize);

        // Get route details
        $route = $this->request->getAttribute('route');
        $routeName = $route->getName();
        $routeArgs = $route->getArguments();

        // Generate pagination links
        $links['self'] = "" . $this->request->getUri();
        $links['first'] = $routeParser->fullUrlFor($uri, $routeName, $routeArgs, ['page[number]' => 1, 'page[size]' => $pageSize]);
        $links['last'] = $routeParser->fullUrlFor($uri, $routeName, $routeArgs, ['page[number]' => $lastPageNumber, 'page[size]' => $pageSize]);

        if ($pageNumber > 1) {
            $links['prev'] = $routeParser->fullUrlFor($uri, $routeName, $routeArgs, ['page[number]' => ($pageNumber - 1), 'page[size]' => $pageSize]);
        } else {
            $links['prev'] = null;
        }

        if ($pageNumber < $lastPageNumber) {
            $links['next'] = $routeParser->fullUrlFor($uri, $routeName, $routeArgs, ['page[number]' => ($pageNumber + 1), 'page[size]' => $pageSize]);
        } else {
            $links['next'] = null;
        }

        return $links;
    }
}
