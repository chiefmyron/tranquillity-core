<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Exception;

use Exception;
use Throwable;

abstract class DomainException extends Exception
{
    protected string $errorCode = '';  // Application-specific error code
    protected string $title = '';      // Human-readable summary of the problem that should not change from occurrence to occurrence of the problem
    protected string $detail = '';
    protected string $sourceType = '';
    protected string $sourceValue = '';

    public function __construct(
        string $detail,
        string $sourceType = '',
        string $sourceValue = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($detail, $this->code, $previous);

        $this->detail = $detail;
        $this->sourceType = $sourceType;
        $this->sourceValue = $sourceValue;
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getSource(): ?array
    {
        if (trim($this->sourceType) == '') {
            return null;
        }
        return [$this->sourceType => $this->sourceValue];
    }
}
