<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\User;

use PHPUnit\Framework\TestCase;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\User\UserId;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Domain\Validation\NotificationError;

class UserTest extends TestCase
{

    public function testCanBeCreatedWithAllValidValues()
    {
        $this->assertInstanceOf(
            User::class,
            new User(UserId::create(), "patto@live.com.au", "password", "Australia/Brisbane", "en_AU")
        );
    }

    public function testCannotBeCreatedWithInvalidTimezone()
    {
        $this->expectException(ValidationException::class);
        $user = new User(UserId::create(), "patto@live.com.au", "password", "Brisbane/Australia", "en_AU");
    }

    public function testCannotBeCreatedWithInvalidLocale()
    {
        $this->expectException(ValidationException::class);
        $user = new User(UserId::create(), "patto@live.com.au", "password", "Australia/Brisbane", "thisisarubbishlocale");
    }
}
