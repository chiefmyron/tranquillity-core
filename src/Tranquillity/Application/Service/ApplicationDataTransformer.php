<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service;

use Tranquillity\Domain\Validation\Notification;

interface ApplicationDataTransformer
{
    /**
     * @return mixed
     */
    public function read();

    /**
     * Write a single error instead of entity data
     *
     * @param string $code
     * @param string $detail
     * @param string $source
     * @param string $field
     * @param array $meta
     * @return void
     */
    public function writeError(string $code, string $detail, string $source = '', string $field = '', array $meta = []): void;

    /**
     * Write a collection of validation errors instead of entity data
     *
     * @param Notification $notification
     * @return void
     */
    public function writeValidationError(Notification $notification): void;
}
