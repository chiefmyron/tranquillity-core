<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\User;

use Tranquillity\Domain\Model\DomainValueObject;

class HashedPassword
{
    private string $hashedPassword;

    public function __construct(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    public function equals(self $object): bool
    {
        return $this->toString() == $object->toString();
    }

    public function toString()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return $this->hashedPassword;
    }
}
