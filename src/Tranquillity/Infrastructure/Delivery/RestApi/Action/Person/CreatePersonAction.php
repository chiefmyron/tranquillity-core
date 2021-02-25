<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\Person\CreatePersonRequest;
use Tranquillity\Application\Service\Person\CreatePersonService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApiPersonDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Enum\ResourceTypeEnum;

class CreatePersonAction
{
    private CreatePersonService $service;
    private TransactionalSession $txnSession;

    public function __construct(CreatePersonService $service, TransactionalSession $txn)
    {
        $this->service = $service;
        $this->txnSession = $txn;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Get data from request
        $payload = $request->getParsedBody();
        $data = $payload['data'] ?? array();

        // Validate resource type in request
        if ($data['type'] != ResourceTypeEnum::PERSON) {
            throw new \Exception("Resource type of '" . ResourceTypeEnum::PERSON . "' is required");
        }

        // Build request to create new person from payload
        $createPersonRequest = CreatePersonRequest::createFromArray($data['attributes']);

        // Execute transaction to create new person
        $txnService = new TransactionalService($this->service, $this->txnSession);
        $person = $txnService->execute($createPersonRequest, new JsonApiPersonDataTransformer($request));
        return JsonApiResponder::writeResponse($response, $person, HttpStatusCodeEnum::CREATED);
    }
}
