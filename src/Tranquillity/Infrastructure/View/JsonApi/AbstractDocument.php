<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\View\JsonApi;

use Carbon\Carbon;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Tranquillity\Domain\Model\DomainEntity;
use Tranquillity\Domain\Validation\Notification;

abstract class AbstractDocument
{
    const RESOURCE = 'resource';
    const RESOURCE_COLLECTION = 'resourceCollection';
    const ERROR_COLLECTION = 'errorCollection';

    /**
     * Top-level members of the resource document
     *
     * @var array
     */
    protected $members = [];

    /**
     * Flag to indicate whether the resource represents a collection of data
     *
     * @var boolean
     */
    protected $isCollection = false;

    /**
     * Flag to indicate whether the resource represents one or more errors
     *
     * @var boolean
     */
    protected $isError = false;

    /**
     * Factory for creating top-level JSON:API response documents
     *
     * @param mixed $entity Domain entity
     * @param ServerRequestInterface $request
     * @param RouteParserInterface $routeParser
     * @param string $documentType
     * @return Document
     */
    public static function createDocument(DomainEntity $entity, ServerRequestInterface $request, RouteParserInterface $routeParser, $documentType = '')
    {
        if ($documentType == self::ERROR_COLLECTION || $entity instanceof Notification) {
            return new ErrorCollectionDocument($entity, $request, $routeParser);
        } elseif ($documentType == self::RESOURCE_COLLECTION || is_array($entity) || is_iterable($entity)) {
            return new ResourceCollectionDocument($entity, $request, $routeParser);
        } elseif ($documentType == self::RESOURCE || is_null($entity) == false) {
            return new ResourceDocument($entity, $request, $routeParser);
        } else {
            throw new \Exception("Unable to determine correct document type to use.");
        }
    }

    /**
     * Shows whether the document represents a collection of entries
     *
     * @return boolean
     */
    public function isCollection()
    {
        return $this->isCollection;
    }

    /**
     * Shows whether the document represents an error or collection of errors
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->isError;
    }

    /**
     * Check if the top-level member of the document exists
     *
     * @param string $memberName
     * @return bool
     */
    public function hasMember(string $memberName)
    {
        if (!array_key_exists($memberName, $this->members)) {
            return false;
        }

        return true;
    }

    /**
     * Get data from a member of the document
     *
     * @param string  $memberName  Name of the document member to retrieve data for
     * @param bool    $asArray     If true, returns all data underneath the member in an array
     * @return mixed
     */
    public function getMember(string $memberName, bool $asArray = false)
    {
        if (!array_key_exists($memberName, $this->members)) {
            return null;
        }

        $member = $this->members[$memberName];
        if ($asArray == true) {
            return $this->resolveDocumentMembers($member);
        }
        return $member;
    }

    /**
     * Converts the document into an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->resolveDocumentMembers($this->members);
    }

    /**
     * Iterates through an array representation of document members and recursively
     * processes any related document components.
     *
     * @param array $data
     * @return array
     */
    private function resolveDocumentMembers($data)
    {
        // Recursively resolve embedded resources and correctly format values
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // If value is an array, resolve all elements of the array
                $data[$key] = $this->resolveDocumentMembers($value);
            } elseif ($value instanceof \DateTime) {
                // If value is a DateTime value, convert to ISO8601 valid string
                $data[$key] = Carbon::instance($value)->toIso8601String();
            } elseif ($value instanceof AbstractDocument) {
                // If value is a document component, convert it to an array and then resolve all elements of that array
                $documentComponent = $value->toArray();
                $data[$key] = $this->resolveDocumentMembers($documentComponent);
            }
        }

        return $data;
    }
}
