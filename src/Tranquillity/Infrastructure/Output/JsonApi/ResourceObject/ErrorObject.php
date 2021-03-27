<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\ResourceObject;

use Ramsey\Uuid\Uuid;
use Tranquillity\Domain\Enum\ErrorCodeEnum;
use Tranquillity\Domain\Validation\Error;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class ErrorObject
{
    private string $id;
    private string $aboutLink = '';
    private int $httpStatusCode = -1;
    private string $code = '';
    private string $title = '';
    private string $detail;
    private array $source = [];
    private array $meta;

    public function __construct(
        string $code,
        string $detail = '',
        array $meta = []
    ) {
        $this->id = Uuid::uuid1()->toString();
        $this->setCode($code);
        $this->detail = $detail;
        $this->meta = $meta;
    }

    public static function createFromValidationError(Error $error): self
    {
        return new ErrorObject(
            $error->getErrorCode(),
            $error->getDetail(),
            $error->getMeta()
        );
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
        $this->title = ErrorCodeEnum::title($code);
        $this->httpStatusCode = HttpStatusCodeEnum::findErrorStatusCode($code);
        // TODO: Set 'about' link here too, based on error code
    }

    public function setSource(string $sourceType, string $sourceValue): void
    {
        $sourceType = trim(strtolower($sourceType));
        if ($sourceType == '') {
            $this->source = [];
            return;
        }

        if ($sourceType != 'pointer' && $sourceType != 'parameter') {
            throw new \InvalidArgumentException('Error source type must be either "pointer" or "parameter" ("' . $sourceType . '" was provided).');
        }

        $this->source = [$sourceType => $sourceValue];
    }

    public function render(): array
    {
        // Build up error object detail
        $error = [
            'id' => $this->id,
            'status' => $this->httpStatusCode,
            'code' => $this->code,
            'title' => $this->title,
        ];

        // Add extra detail if it has been provided
        if ($this->detail != '') {
            $error['detail'] = $this->detail;
        }
        if ($this->aboutLink != '') {
            $error['link'] = ['about' => $this->aboutLink];
        }
        if (count($this->source) > 0) {
            $error['source'] = $this->source;
        }

        // If we are displaying error details, include them in the error metadata
        if (count($this->meta) > 0) {
            $error['meta'] = $this->meta;
        }

        return $error;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
