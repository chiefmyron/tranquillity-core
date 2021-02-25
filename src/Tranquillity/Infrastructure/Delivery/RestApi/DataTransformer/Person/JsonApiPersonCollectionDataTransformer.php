<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person;

use Tranquillity\Application\DataTransformer\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\PersonCollection;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\ResourceCollectionDataTransformer;

class JsonApiPersonCollectionDataTransformer extends ResourceCollectionDataTransformer implements PersonCollectionDataTransformer
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
        $dataTransformer = new JsonApiPersonDataTransformer($this->request);

        // Generate resource objects for each element of the collection
        $collection = $personCollection->collection();
        foreach ($collection as $person) {
            // Generate main resource object data
            $dataTransformer->write($person, $fields, $relatedResources);
            $personResource = $dataTransformer->read();

            // Generate 'self' link for the resource
            $uri = $this->request->getUri();
            $link = $this->routeParser->fullUrlFor($uri, 'person-detail', ['id' => $personResource['data']['id']]);
            $personResource['data']['link'] = ['self' => $link];

            // Add resource object to array
            $this->data[] = $personResource['data'];
        }

        $this->links = $this->writeLinks($personCollection);

        $this->meta['totalRecords'] = $personCollection->totalRecordCount();
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

        // Calculate pagination limits
        $lastPageNumber = ceil($totalRecordCount / $pageSize);

        // Generate pagination links
        $uri = $this->request->getUri();
        $links['self'] = "" . $uri;
        $links['first'] = $this->routeParser->fullUrlFor($uri, $this->getRouteName(), $this->getRouteArguments(), ['page[number]' => 1, 'page[size]' => $pageSize]);
        $links['last'] = $this->routeParser->fullUrlFor($uri, $this->getRouteName(), $this->getRouteArguments(), ['page[number]' => $lastPageNumber, 'page[size]' => $pageSize]);

        if ($pageNumber > 1) {
            $links['prev'] = $this->routeParser->fullUrlFor($uri, $this->getRouteName(), $this->getRouteArguments(), ['page[number]' => ($pageNumber - 1), 'page[size]' => $pageSize]);
        } else {
            $links['prev'] = null;
        }

        if ($pageNumber < $lastPageNumber) {
            $links['next'] = $this->routeParser->fullUrlFor($uri, $this->getRouteName(), $this->getRouteArguments(), ['page[number]' => ($pageNumber + 1), 'page[size]' => $pageSize]);
        } else {
            $links['next'] = null;
        }

        return $links;
    }
}
