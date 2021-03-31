<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

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

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->hashedPassword;
    }
}
