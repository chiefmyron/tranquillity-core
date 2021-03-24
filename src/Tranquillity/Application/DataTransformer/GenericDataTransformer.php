<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer;

use Tranquillity\Domain\Validation\Notification;

interface GenericDataTransformer
{
    /**
     * @return mixed
     */
    public function read();

    /**
     * Write a collection of validation errors instead of entity data
     *
     * @param Notification $notification
     * @return void
     */
    public function writeValidationError(Notification $notification): void;
}
