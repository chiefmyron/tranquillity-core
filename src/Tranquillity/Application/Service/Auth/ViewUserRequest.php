<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\Auth;

class ViewUserRequest
{
    private string $id;
    private array $fields;
    private array $relatedResources;

    public function __construct(
        string $id,
        array $fields = [],
        array $relatedResources = []
    ) {
        $this->id = $id;
        $this->fields = $fields;
        $this->relatedResources = $relatedResources;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function relatedResources(): array
    {
        return $this->relatedResources;
    }
}
