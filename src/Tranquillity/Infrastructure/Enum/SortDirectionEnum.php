<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Enum;

/**
 * Enumeration of query string sort direction parameter logical operators
 *
 * @package Tranquillity\System\Enums
 * @author  Andrew Patterson <patto@live.com.au>
 */

class SortDirectionEnum extends AbstractEnum
{
    public const ASCENDING  = 'ASC';
    public const DESCENDING = 'DESC';
}
