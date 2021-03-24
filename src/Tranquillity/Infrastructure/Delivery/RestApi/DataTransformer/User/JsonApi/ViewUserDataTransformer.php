<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApi;

use Tranquillity\Application\DataTransformer\User\UserDataTransformer;
use Tranquillity\Domain\Model\User\User;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\UserResourceObject;

class ViewUserDataTransformer extends AbstractDataTransformer implements UserDataTransformer
{
    public function write(User $entity, array $fields = [], array $relatedResources = []): void
    {
        // Create resource object that represents the person
        $resourceObject = new UserResourceObject($this->routeCollector);
        $resourceObject->populate($entity, $fields, $relatedResources);

        // Generate data collection document
        $this->document = new DataDocument($resourceObject);
    }
}
