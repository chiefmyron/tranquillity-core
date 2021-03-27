<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi;

use Tranquillity\Application\DataTransformer\Person\PersonCollectionDataTransformer;
use Tranquillity\Domain\Model\Person\PersonCollection;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataCollectionDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\PersonResourceCollection;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class ListPeopleDataTransformer extends AbstractDataTransformer implements PersonCollectionDataTransformer
{
    public function write(PersonCollection $collection, array $fields = [], array $relatedResources = []): void
    {
        // Create resource collection containing the set of Person entities
        $resourceObjects = new PersonResourceCollection($this->routeCollector);
        $resourceObjects->populate($collection, $fields, $relatedResources);

        // Generate data collection document
        $document = new DataCollectionDocument($resourceObjects);

        // Generate REST API response
        $this->apiResponse = new RestResponse($document, HttpStatusCodeEnum::OK);
    }

    public function setErrorSource(ErrorObject $errorObject, string $source, string $field): ErrorObject
    {
        return $errorObject;
    }
}
