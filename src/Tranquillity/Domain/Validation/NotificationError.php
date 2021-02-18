<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Validation;

use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Exception\DomainException;

class NotificationError
{
    protected int $status; // HTTP status code applicable to this notification
    protected string $code; // Application-specific error code
    protected string $title; // A short, human-readable summary of the problem that should not change from occurrence to occurrence of the problem
    protected string $detail;
    protected string $sourceType;
    protected string $sourceValue;
    protected array $meta;

    final private function __construct(
        int $status,
        string $code,
        string $title,
        string $detail = '',
        string $sourceType = '',
        string $sourceValue = '',
        array $meta = []
    ) {
        $this->status = $status;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->sourceType = $sourceType;
        $this->sourceValue = $sourceValue;
        $this->meta = $meta;
    }

    public static function create(
        string $code,
        string $detail = '',
        string $sourceType = '',
        string $sourceValue = '',
        array $meta = []
    ): self {
        // TODO: Add resource lookups for HTTP status code and title based on supplied application error code
        $status = ErrorCodeEnum::statusCode($code);
        $title = ErrorCodeEnum::title($code);

        return new static(
            $status,
            $code,
            $title,
            $detail,
            $sourceType,
            $sourceValue,
            $meta
        );
    }

    public static function createFromDomainException(DomainException $exception): self
    {
        // Extract error source from exception
        $sourceType = '';
        $sourceValue = '';
        $source = $exception->getSource();
        if (is_null($source) == false) {
            $sourceType = array_key_first($source) ?? '';
            $sourceValue = $source[$sourceType] ?? '';
        }

        return new static(
            $exception->getStatusCode(),
            $exception->getErrorCode(),
            $exception->getTitle(),
            $exception->getDetail(),
            $sourceType,
            $sourceValue
        );
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getErrorCode(): string
    {
        return $this->code;
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

    public function getMeta(): array
    {
        return $this->meta;
    }
}
