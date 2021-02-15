<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\View\JsonApi;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Tranquillity\Domain\Model\DomainEntity;

class ResourceDocument extends AbstractDocument
{
    /**
     * Creates a new JSON:API response document, representing a single domain
     * entity
     *
     * @param DomainEntity $entity
     * @param ServerRequestInterface $request
     * @param RouteParserInterface $router
     * @return void
     */
    public function __construct(DomainEntity $entity, ServerRequestInterface $request, RouteParserInterface $router)
    {
        // Set flags for this document type
        $this->isError = false;
        $this->isCollection = false;

        // Add mandatory document member data
        $this->members['data'] = [];
        $this->members['jsonapi'] = [];

        // Add optional document member data if it exists
        $meta = $this->getMetaObject($entity, $request);
        if (count($meta) > 0) {
            $this->members['meta'] = $meta;
        }

        $links = $this->getLinksObject($entity, $request);
        if (count($links) > 0) {
            $this->members['links'] = $links;
        }

        $included = $this->getIncludedObject($entity, $request);
        if (count($included) > 0) {
            $this->members['included'] = $included;
        }
    }

    /**
     * Generates a meta object for the primary data represented by the document
     *
     * @param DomainEntity $entity
     * @param ServerRequestInterface $request
     * @return array
     */
    private function getMetaObject(DomainEntity $entity, ServerRequestInterface $request): array
    {
        $meta = [];
        return $meta;
    }

    /**
     * Generates a links object for the primary data represented by the document
     *
     * @param DomainEntity $entity
     * @param ServerRequestInterface $request
     * @return array
     */
    private function getLinksObject(DomainEntity $entity, ServerRequestInterface $request): array
    {
        $links = [];
        $links['self'] = (string)$request->getUri();
        return $links;
    }

    /**
     * Generates an included object for the primary data represented by the document
     *
     * @param DomainEntity $entity
     * @param ServerRequestInterface $request
     * @return array
     */
    private function getIncludedObject(DomainEntity $entity, ServerRequestInterface $request): array
    {
        $included = [];

        // Check to see if the client has requested a compound document
        $queryStringParams = $request->getQueryParams();
        $include = $queryStringParams['include'] ?? '';
        if ($include == "") {
            return [];
        }

        // Add include for each specified entity type
        $includeTypes = explode(",", $include);
        foreach ($includeTypes as $includesPath) {
            $included = $this->getIncludedObjectDetail($included, $includesPath, $entity, $request);
        }

        return $included;
    }

    /**
     * Gets the resource document for an included resource
     *
     * @param array $included
     * @param string $entityPath
     * @param DomainEntity $entity
     * @param ServerRequestInterface $request
     * @return array
     */
    private function getIncludedObjectDetail(array $included, string $entityPath, DomainEntity $entity, ServerRequestInterface $request): array
    {
        // Explode out the entity name, in case it has been specified as a multi-part path
        $entityPathParts = explode('.', $entityPath, 2);

        // Check that the first 'include' entity specified in the path is valid for the current parent entity
        $includeName = $entityPathParts[0];
        if (is_null($entity->$includeName) == true) {
            throw new \Exception("Unable to include resource '" . $includeName . "'");
        }

        // Build a resource document for the first entity specified in the path
        $childEntity = $entity->$includeName;
        if (is_iterable($childEntity)) {
            // Child entity is a collection - add each element in the collection
            foreach ($childEntity as $child) {
                $included[] = new ResourceDocumentComponent($child, $request);
            }
        } else {
            // Child entity is a single resource - add directly
            $included[] = new ResourceDocumentComponent($childEntity, $request);
        }

        // If there are other entities specified in the remaineder of the multi-part path, continue adding them
        if (isset($entityPathParts[1]) && trim($entityPathParts[1]) != '') {
            $included = $this->getIncludedObjectDetail($included, $entityPathParts[1], $childEntity, $request);
        }

        return $included;
    }
}
