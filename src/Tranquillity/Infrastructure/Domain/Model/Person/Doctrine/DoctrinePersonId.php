<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Person\Doctrine;

use Tranquillity\Domain\Model\Person\PersonId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DoctrinePersonId extends UuidBinaryOrderedTimeType
{
    public function getName()
    {
        return 'PersonId';
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return PersonId The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $uuid = parent::convertToPHPValue($value, $platform);
        if ($uuid instanceof UuidInterface) {
            return PersonId::create($uuid->toString());
        }
        return PersonId::create($value);
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $uuid = Uuid::fromString($value->id());
        return parent::convertToDatabaseValue($uuid, $platform);
    }
}
