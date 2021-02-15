<?php

declare(strict_types=1);

namespace Tranquillity\Application\Service;

class TransactionalService implements ApplicationService
{
    private ApplicationService $service;
    private TransactionalSession $session;

    public function __construct(ApplicationService $service, TransactionalSession $session)
    {
        $this->service = $service;
        $this->session = $session;
    }

    public function execute($request = null, $dataTransformer = null)
    {
        $operation = function () use ($request, $dataTransformer) {
            return $this->service->execute($request, $dataTransformer);
        };

        return $this->session->executeTransaction($operation);
    }
}
