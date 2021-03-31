<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Domain\Model\Auth\Doctrine;

use Tranquillity\Domain\Model\Auth\UserId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DoctrineUserId extends UuidBinaryOrderedTimeType
{
    public function getName()
    {
        return 'UserId';
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return UserId The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $uuid = parent::convertToPHPValue($value, $platform);
        if ($uuid instanceof UuidInterface) {
            return UserId::create($uuid->toString());
        }
        return UserId::create($value);
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
        if ($value === null) {
            return parent::convertToDatabaseValue(null, $platform);
        }

        $uuid = Uuid::fromString($value->id());
        return parent::convertToDatabaseValue($uuid, $platform);
    }
}