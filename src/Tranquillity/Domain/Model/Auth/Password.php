<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Model\Auth;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Domain\Validation\Notification;

class Password
{
    private const PASSWORD_LENGTH_MIN = 6;
    private string $password;

    public function __construct(string $password)
    {
        $this->password = trim($password);

        // Ensure value object is valid after creation
        $errors = $this->validate();
        if ($errors->hasErrors() === true) {
            throw new ValidationException(
                "Validation errors occurred while creating instance of " . self::class,
                $errors->getErrors(),
                422
            );
        }
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->password;
    }

    public function equals(self $object): bool
    {
        return $this->toString() == $object->toString();
    }

    /**
     * Validate the Password value object
     *
     * @return Notification
     */
    public function validate(): Notification
    {
        $notification = new Notification();

        // Check password length
        if (strlen($this->password) < self::PASSWORD_LENGTH_MIN) {
            $notification->addItem(ErrorCodeEnum::FIELD_VALIDATION_PASSWORD_INVALID, "Password must be at least " . self::PASSWORD_LENGTH_MIN . " characters long", 'attribute', 'password');
        }

        return $notification;
    }
}
