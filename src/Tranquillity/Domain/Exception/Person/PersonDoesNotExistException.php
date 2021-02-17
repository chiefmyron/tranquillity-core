<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Exception\Person;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\DomainException;
use Tranquillity\Domain\Enum\HttpStatusCodeEnum;

class PersonDoesNotExistException extends DomainException
{
    protected $code = HttpStatusCodeEnum::NOT_FOUND;
    protected string $errorCode = ErrorCodeEnum::PERSON_DOES_NOT_EXIST;
    protected string $title = "Unable to find person with the specified ID";
}
