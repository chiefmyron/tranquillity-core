<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Enum;

/**
 * Enumeration of query string filter parameter logical operators
 *
 * @package Tranquillity\System\Enums
 * @author  Andrew Patterson <patto@live.com.au>
 */

class FilterOperatorEnum extends AbstractEnum
{
    public const EQUALS             = 'eq';
    public const NOT_EQUALS         = '!eq';
    public const IN                 = 'in';
    public const NOT_IN             = '!in';
    public const IS_NULL            = 'null';
    public const IS_NOT_NULL        = '!null';
    public const LIKE               = 'like';
    public const NOT_LIKE           = '!like';
    public const GREATER_THAN       = 'gt';
    public const GREATER_THAN_EQUAL = 'gte';
    public const LESS_THAN          = 'lt';
    public const LESS_THAN_EQUAL    = 'lte';
}
