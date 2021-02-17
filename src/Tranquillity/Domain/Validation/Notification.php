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
     * Use message and triggering exception to add a new NotificationError to the Notification
     *
     * @param string $message
     * @param \Exception|null $exception
     * @return void
     */
    public function addItem(
        string $code,
        string $detail = '',
        string $sourceType = '',
        string $sourceValue = '',
        array $meta = []
    ): void {
        $item = NotificationError::create($code, $detail, $sourceType, $sourceValue, $meta);
        $this->addNotificationError($item);
    }

    /**
     * Use detail from a DomainException to add a new NotificationError to the Notification
     *
     * @param DomainException $exception
     * @return void
     */
    public function addDomainException(DomainException $exception): void
    {
        $item = NotificationError::createFromDomainException($exception);
        $this->addNotificationError($item);
    }

    /**
     * Add new NotificationError to the Notification
     *
     * @param NotificationError $item
     * @return void
     */
    public function addNotificationError(NotificationError $item)
    {
        $this->notifications[] = $item;
    }

    /**
     * Check if the Notification contains at least one error
     *
     * @return boolean
     */
    public function hasItems(): bool
    {
        if (count($this->notifications) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Return the set of NotificationErrors
     *
     * @return array<NotificationError>
     */
    public function getItems(): array
    {
        return $this->notifications;
    }
}
