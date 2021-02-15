<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\View\JsonApi\Component;

use Carbon\Carbon;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractComponent
{
    /**
     * Top-level members of the resource document
     *
     * @var array
     */
    protected $members = [];

    /**
     * Check if specified member of the document component exists
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
     * Create a new document component instance
     *
     * @param  mixed                                    $entity   The resource object or array of resource objects
     * @param \Psr\Http\Message\ServerRequestInterface  $request  PSR-7 HTTP request object
     * @return mixed
     */
    abstract public function __construct($entity, ServerRequestInterface $request);

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
     * Convert document component object into an array representation
     *
     * @return array
     */
    public function toArray()
    {
        return $this->resolveDocumentMembers($this->members);
    }

    /**
     * Apply sparse fieldset filters specified on the query string to the set of fields provided
     *
     * @param string                                   $entityType  The type of entity to check for specified filters
     * @param array                                    $fields      The set of fields that needs to be filtered
     * @param \Psr\Http\Message\ServerRequestInterface $request     PSR7 request
     * @return array
     */
    protected function applySparseFieldset(string $entityType, array $fields, ServerRequestInterface $request)
    {
        // Get array of query string parameters from request
        $queryStringParams = $request->getQueryParams();

        // Check to see if a sparse fieldset has been applied to the entity
        $filters = $queryStringParams['fields'] ?? '';
        if (isset($fields[$entityType])) {
            $filterFieldName = explode(",", $filters[$entityType]);
            foreach ($fields as $attributeName => $value) {
                if (!in_array($attributeName, $filterFieldName)) {
                    unset($fields[$attributeName]);
                }
            }
        }
        return $fields;
    }

    /**
     * Iterates through an array representation of document members and recursively
     * processes any related document components.
     *
     * @param AbstractComponent|array $data
     * @return array
     */
    private function resolveDocumentMembers($data)
    {
        // Convert a document component into an array before starting
        if ($data instanceof AbstractComponent) {
            $data = $data->toArray();
        }

        // Recursively resolve embedded resources and correctly format values
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // If value is an array, resolve all elements of the array
                $data[$key] = $this->resolveDocumentMembers($value);
            } elseif ($value instanceof \DateTime) {
                // If value is a DateTime value, convert to ISO8601 valid string
                $data[$key] = Carbon::instance($value)->toIso8601String();
            } elseif ($value instanceof AbstractComponent) {
                // If value is a document component, convert it to an array and then resolve all elements of that array
                $documentComponent = $value->toArray();
                $data[$key] = $this->resolveDocumentMembers($documentComponent);
            }
        }

        return $data;
    }
}
