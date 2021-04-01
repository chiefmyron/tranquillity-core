<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApi;

use Tranquillity\Application\Service\CreatePerson\CreatePersonDataTransformer;
use Tranquillity\Application\Service\UpdatePerson\UpdatePersonDataTransformer;
use Tranquillity\Application\Service\ViewPerson\ViewPersonDataTransformer;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\PersonResourceObject;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class PersonDataTransformer extends AbstractDataTransformer implements
    ViewPersonDataTransformer,
    CreatePersonDataTransformer,
    UpdatePersonDataTransformer
{
    public function write(Person $entity, array $fields = [], array $relatedResources = []): void
    {
        // Create resource object that represents the person
        $resourceObject = new PersonResourceObject($this->routeCollector);
        $resourceObject->populate($entity, $fields, $relatedResources);

        // Generate data collection document
        $document = new DataDocument($resourceObject);

        // Generate REST API response
        $this->apiResponse = new RestResponse($document, HttpStatusCodeEnum::OK);
    }

    public function setErrorSource(ErrorObject $errorObject, string $source, string $field): ErrorObject
    {
        if ($field == '') {
            return $errorObject;
        }

        switch ($field) {
            case 'id':
                $errorObject->setSource('pointer', '/data/id');
                break;
            case 'type':
                $errorObject->setSource('pointer', '/data/type');
                break;
            default:
                $errorObject->setSource('pointer', '/data/attributes/' . $field);
                break;
        }
        return $errorObject;
    }
}
