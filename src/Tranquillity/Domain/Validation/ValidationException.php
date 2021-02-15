<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Validation;

final class ValidationException extends \RuntimeException
{
    private Notification $notifications;

    public function __construct(string $message, Notification $notifications, int $code, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->notifications = $notifications;
    }

    public function getNotifications(): Notification
    {
        return $this->notifications;
    }
}
