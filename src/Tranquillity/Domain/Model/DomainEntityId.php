<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model;

use Ramsey\Uuid\Uuid;

abstract class DomainEntityId
{
    /**
     * @var string
     */
    private string $id;

    /**
     * Constructor
     *
     * @param string|null $id
     */
    final private function __construct(?string $id = null)
    {
        if (is_null($id)) {
            $id = Uuid::uuid1()->toString();
        }
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Check equality of entity IDs
     *
     * @param self $entityId
     * @return boolean
     */
    public function equals(self $entityId): bool
    {
        return $entityId->id() === $this->id();
    }

    /**
     * Create new entity ID (as UUID v1). If no ID is provided,
     * one will automatically be generated
     *
     * @param string|null $id
     * @return self
     */
    public static function create(?string $id = null): self
    {
        return new static($id);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id();
    }
}
