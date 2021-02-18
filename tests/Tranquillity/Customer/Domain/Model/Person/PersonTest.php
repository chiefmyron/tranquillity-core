<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Person;

use Error;
use PHPUnit\Framework\TestCase;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Validation\Notification;
use Tranquillity\Domain\Validation\NotificationError;

class PersonTest extends TestCase
{

    public function testCanBeCreatedWithValidEmailAddress()
    {
        $this->assertInstanceOf(
            Person::class,
            new Person(PersonId::create(), "Steve", "Rogers", "First Avenger", "steve.rogers@avengers.com")
        );
    }

    public function testCannotBeCreatedWithMissingFirstName()
    {
        $this->expectException(ValidationException::class);

        try {
            $person = new Person(PersonId::create(), "", "Rogers", "First Avenger", "steve.rogers@avengers.com");
        } catch (ValidationException $ex) {
            $this->assertInstanceOf(Notification::class, $ex->getNotification());
            $notification = $ex->getNotification();

            $this->assertTrue($notification->hasItems());
            $notificationItems = $notification->getItems();

            $this->assertContainsOnlyInstancesOf(NotificationError::class, $notification->getItems());
            foreach ($notificationItems as $notificationItem) {
                $errorCode = $notificationItem->getErrorCode();
                $this->assertEquals(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, $notificationItem->getErrorCode());
                $this->assertEquals(ErrorCodeEnum::statusCode($errorCode), $notificationItem->getStatusCode());
                $this->assertEquals(ErrorCodeEnum::title($errorCode), $notificationItem->getTitle());
                $this->assertEquals("Mandatory field 'firstName' has not been provided", $notificationItem->getDetail());

                $source = $notificationItem->getSource();
                $this->assertIsArray($source);
                $this->assertArrayHasKey('pointer', $source);
                $this->assertContains('/data/attributes/firstName', $source);
            }

            throw $ex;
        }
    }

    public function testCannotBeCreatedWithMissingLastName()
    {
        $this->expectException(ValidationException::class);

        try {
            $person = new Person(PersonId::create(), "Steve", "", "First Avenger", "steve.rogers@avengers.com");
        } catch (ValidationException $ex) {
            $this->assertInstanceOf(Notification::class, $ex->getNotification());
            $notification = $ex->getNotification();

            $this->assertTrue($notification->hasItems());
            $notificationItems = $notification->getItems();

            $this->assertContainsOnlyInstancesOf(NotificationError::class, $notification->getItems());
            foreach ($notificationItems as $notificationItem) {
                $errorCode = $notificationItem->getErrorCode();
                $this->assertEquals(ErrorCodeEnum::FIELD_VALIDATION_MANDATORY_VALUE_MISSING, $notificationItem->getErrorCode());
                $this->assertEquals(ErrorCodeEnum::statusCode($errorCode), $notificationItem->getStatusCode());
                $this->assertEquals(ErrorCodeEnum::title($errorCode), $notificationItem->getTitle());
                $this->assertEquals("Mandatory field 'lastName' has not been provided", $notificationItem->getDetail());

                $source = $notificationItem->getSource();
                $this->assertIsArray($source);
                $this->assertArrayHasKey('pointer', $source);
                $this->assertContains('/data/attributes/lastName', $source);
            }

            throw $ex;
        }
    }

    public function testCannotBeCreatedWithMissingEmailAddress()
    {
        $this->expectException(ValidationException::class);
        $person = new Person(PersonId::create(), "Steve", "Rogers", "First Avenger", "");
    }

    public function testCannotBeCreatedWithInvalidEmailAddress()
    {
        $this->expectException(ValidationException::class);
        $person = new Person(PersonId::create(), "Steve", "Rogers", "First Avenger", "steve...");
    }
}
