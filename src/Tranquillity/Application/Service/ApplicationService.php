<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service;

interface ApplicationService
{
    /**
     * @param $request
     * @return mixed
     */
    public function execute($request = null);
}
