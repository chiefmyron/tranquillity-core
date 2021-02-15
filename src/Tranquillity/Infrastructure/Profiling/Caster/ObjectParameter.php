<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\Caster;

final class ObjectParameter
{
    private object $object;
    private ?\Throwable $error;
    private bool $stringable;
    private string $class;

    /**
     * @param object $object
     */
    public function __construct($object, ?\Throwable $error)
    {
        $this->object = $object;
        $this->error = $error;
        $this->stringable = \is_callable([$object, '__toString']);
        $this->class = \get_class($object);
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return \Throwable|null
     */
    public function getError(): ?\Throwable
    {
        return $this->error;
    }

    /**
     * @return boolean
     */
    public function isStringable(): bool
    {
        return $this->stringable;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }
}
