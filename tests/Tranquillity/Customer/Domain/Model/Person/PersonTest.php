<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Person;

use PHPUnit\Framework\TestCase;
use Tranquillity\Domain\Model\Person\Person;
use Tranquillity\Domain\Model\Person\PersonId;
use Tranquillity\Domain\Validation\ValidationException;

class PersonTest extends TestCase
{

    public function testCanBeCreatedWithValidEmailAddress()
    {
        $this->assertInstanceOf(
            Person::class,
            new Person(PersonId::create(), "Steve", "Rogers", "First Avenger", "steve.rogers@avengers.com")
        );
    }

    public function testCannotBeCreatedWithInvalidEmailAddress()
    {
        $this->expectException(ValidationException::class);
        $person = new Person(PersonId::create(), "Steve", "Rogers", "First Avenger", "steve...");
    }
}
