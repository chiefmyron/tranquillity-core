<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

class ViewClientByNameRequest
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
