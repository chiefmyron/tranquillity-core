<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Enum;

/**
 * Enumeration of valid resource types that can be used in JSON:API
 * requests
 *
 * @package Tranquillity\System\Enums
 * @author  Andrew Patterson <patto@live.com.au>
 */

class ResourceTypeEnum extends AbstractEnum
{
    public const PERSON  = 'person';
}
