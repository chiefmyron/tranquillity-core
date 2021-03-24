<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApi;

use Tranquillity\Application\DataTransformer\User\UserCollectionDataTransformer;
use Tranquillity\Domain\Model\User\UserCollection;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataCollectionDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\UserResourceCollection;

class ListUsersDataTransformer extends AbstractDataTransformer implements UserCollectionDataTransformer
{
    public function write(UserCollection $collection, array $fields = [], array $relatedResources = []): void
    {
        // Create resource collection containing the set of User entities
        $resourceObjects = new UserResourceCollection($this->routeCollector);
        $resourceObjects->populate($collection, $fields, $relatedResources);

        // Generate data collection document
        $this->document = new DataCollectionDocument($resourceObjects);
    }
}
