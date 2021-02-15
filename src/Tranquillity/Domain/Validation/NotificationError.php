<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Validation;

class NotificationError
{
    private string $message;
    private ?\Exception $cause = null;

    final private function __construct(string $message, ?\Exception $cause = null)
    {
        $this->message = $message;
        if (is_null($cause) == false) {
            $this->cause = $cause;
        }
    }

    public static function create(string $message, ?\Exception $cause = null): self
    {
        return new static($message, $cause);
    }

    public function message(): string
    {
        return $this->message;
    }

    public function cause(): ?\Exception
    {
        return $this->cause;
    }
}
