<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApi;

use Tranquillity\Application\DataTransformer\User\UserCollectionDataTransformer;
use Tranquillity\Domain\Model\User\UserCollection;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataCollectionDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\UserResourceCollection;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class ListUsersDataTransformer extends AbstractDataTransformer implements UserCollectionDataTransformer
{
    public function write(UserCollection $collection, array $fields = [], array $relatedResources = []): void
    {
        // Create resource collection containing the set of User entities
        $resourceObjects = new UserResourceCollection($this->routeCollector);
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
