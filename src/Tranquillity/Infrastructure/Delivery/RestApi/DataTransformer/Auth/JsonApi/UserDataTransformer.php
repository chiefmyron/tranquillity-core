<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Auth\JsonApi;

use Tranquillity\Application\Service\CreateUser\CreateUserDataTransformer;
use Tranquillity\Application\Service\FindUserByUsername\FindUserByUsernameDataTransformer;
use Tranquillity\Application\Service\UpdateUser\UpdateUserDataTransformer;
use Tranquillity\Application\Service\ViewUser\ViewUserDataTransformer;
use Tranquillity\Domain\Model\Auth\User;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\AbstractDataTransformer;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\UserResourceObject;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class UserDataTransformer extends AbstractDataTransformer implements
    ViewUserDataTransformer,
    CreateUserDataTransformer,
    UpdateUserDataTransformer,
    FindUserByUsernameDataTransformer
{
    public function write(User $entity, array $fields = [], array $relatedResources = []): void
    {
        // Create resource object that represents the person
        $resourceObject = new UserResourceObject($this->routeCollector);
        $resourceObject->populate($entity, $fields, $relatedResources);

        // Generate data collection document
        $document = new DataDocument($resourceObject);

        // Generate REST API response
        $this->apiResponse = new RestResponse($document, HttpStatusCodeEnum::OK);
    }

    public function setErrorSource(ErrorObject $errorObject, string $source, string $field): ErrorObject
    {
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
