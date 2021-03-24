<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Exception;

use Tranquillity\Domain\Validation\Error;

final class ValidationException extends \DomainException
{
    /** @var array<Error> */
    private array $errors;

    /**
     * Constructor
     *
     * @param string $message
     * @param array<Error> $errors
     * @param integer $code
     * @param \Throwable $previous
     */
    public function __construct(string $message, array $errors, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return (count($this->errors) > 0);
    }
}
