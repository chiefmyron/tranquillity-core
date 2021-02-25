<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model;

abstract class DomainEntity
{
    abstract public function getIdValue(): string;

    abstract public function getEntityType(): string;

    public function changeAttribute(string $attributeName, $value): self
    {
        // Standard 'change' function should following the naming convention 'change<AttributeName>'
        $functionName = 'change' . ucfirst($attributeName);
        if (method_exists($this, $functionName) == false) {
            throw new \UnexpectedValueException();
        }
        return $this->$functionName($value);
    }
}
