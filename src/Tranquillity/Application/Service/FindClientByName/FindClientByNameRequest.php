<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindClientByName;

class FindClientByNameRequest
{
    private string $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
