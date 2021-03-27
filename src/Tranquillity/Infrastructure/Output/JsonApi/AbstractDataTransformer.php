<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi;

use Slim\Interfaces\RouteCollectorInterface;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Infrastructure\Output\JsonApi\Document\ErrorDocument;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;

abstract class AbstractDataTransformer
{
    protected RouteCollectorInterface $routeCollector;
    protected RestResponse $apiResponse;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
        $this->apiResponse = new RestResponse();
    }

    public function read(): RestResponse
    {
        return $this->apiResponse;
    }

    public function writeError(string $code, string $detail, string $source = '', string $field = '', array $meta = []): void
    {
        $errorObject = new ErrorObject($code, $detail, $meta);
        $errorObject = $this->setErrorSource($errorObject, $source, $field);

        // Generate error document
        $errorObjects = [$errorObject];
        $document = new ErrorDocument($errorObjects);
        $this->apiResponse = new RestResponse($document, $document->getHttpStatusCode());
    }

    public function writeValidationError(Notification $notification): void
    {
        $errorObjects = [];
        foreach ($notification->getErrors() as $error) {
            $errorObject = ErrorObject::createFromValidationError($error);
            $errorObject = $this->setErrorSource($errorObject, $error->getSource(), $error->getFieldName());
            $errorObjects[] = $errorObject;
        }

        // Generate error document
        $document = new ErrorDocument($errorObjects);
        $this->apiResponse = new RestResponse($document, $document->getHttpStatusCode());
    }

    abstract protected function setErrorSource(ErrorObject $errorObject, string $source, string $field): ErrorObject;
}
