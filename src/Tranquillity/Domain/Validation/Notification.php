<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Validation;

use Tranquillity\Domain\Exception\DomainException;

/**
 * Implementation of Notification pattern for handling domain validation.
 *
 * @see https://martinfowler.com/eaaDev/Notification.html
 */
class Notification
{
    private array $notifications = [];

    /**
     * Add error directly to the notification
     *
     * @param string $code
     * @param string $detail
     * @param string $sourceType
     * @param string $sourceValue
     * @param array $meta
     * @return void
     */
    public function addItem(
        string $code,
        string $detail = '',
        string $sourceType = '',
        string $sourceValue = '',
        array $meta = []
    ): void {
        $error = Error::create($code, $detail, $sourceType, $sourceValue, $meta);
        $this->addError($error);
    }

    /**
     * Add new Error to the Notification
     *
     * @param Error $error
     * @return void
     */
    public function addError(Error $error): void
    {
        $this->notifications[] = $error;
    }

    /**
     * Add multiple Errors to the Notification
     *
     * @param array<Error> $errors
     * @return void
     */
    public function addErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    /**
     * Check if the Notification contains at least one error
     *
     * @return boolean
     */
    public function hasErrors(): bool
    {
        if (count($this->notifications) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Return the set of Errors
     *
     * @return array<Error>
     */
    public function getErrors(): array
    {
        return $this->notifications;
    }
}
