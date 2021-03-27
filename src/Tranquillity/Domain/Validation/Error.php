<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Validation;

use Tranquillity\Domain\Enum\ErrorCodeEnum;

class Error
{
    protected string $code; // Application-specific error code
    protected string $title; // A short, human-readable summary of the problem that should not change from occurrence to occurrence of the problem
    protected string $detail;
    protected string $source;
    protected string $fieldName;
    protected array $meta;

    final private function __construct(
        string $code,
        string $title,
        string $detail = '',
        string $source = '',
        string $fieldName = '',
        array $meta = []
    ) {
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->source = $source;
        $this->fieldName = $fieldName;
        $this->meta = $meta;
    }

    public static function create(
        string $code,
        string $detail = '',
        string $source = '',
        string $fieldName = '',
        array $meta = []
    ): self {
        $title = ErrorCodeEnum::title($code);
        return new static(
            $code,
            $title,
            $detail,
            $source,
            $fieldName,
            $meta
        );
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

    public function getSource(): string
    {
        return $this->source;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }
}
