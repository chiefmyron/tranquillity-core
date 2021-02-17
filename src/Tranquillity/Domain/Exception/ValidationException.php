<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Exception;

use Tranquillity\Domain\Validation\Notification;

final class ValidationException extends \RuntimeException
{
    private Notification $notification;

    public function __construct(string $message, Notification $notification, int $code, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->notification = $notification;
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}
