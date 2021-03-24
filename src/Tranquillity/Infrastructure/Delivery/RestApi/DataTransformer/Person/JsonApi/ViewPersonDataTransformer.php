<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi;

use Tranquillity\Application\DataTransformer\Person\PersonDataTransformer;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\PersonResourceObject;

class ViewPersonDataTransformer extends AbstractDataTransformer implements PersonDataTransformer
{
    public function write(Person $entity, array $fields = [], array $relatedResources = []): void
    {
        // Create resource object that represents the person
        $resourceObject = new PersonResourceObject($this->routeCollector);
        $resourceObject->populate($entity, $fields, $relatedResources);

        // Generate data collection document
        $this->document = new DataDocument($resourceObject);
    }
}
