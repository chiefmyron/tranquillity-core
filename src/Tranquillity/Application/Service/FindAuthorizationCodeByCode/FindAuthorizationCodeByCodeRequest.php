<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service\FindAuthorizationCodeByCode;

class FindAuthorizationCodeByCodeRequest
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function code(): string
    {
        return $this->code;
    }
}
