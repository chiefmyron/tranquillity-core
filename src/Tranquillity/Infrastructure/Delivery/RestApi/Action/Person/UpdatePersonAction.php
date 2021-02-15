<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\DataTransformer\JsonApi\PersonResourceObjectDataTransformer;
use Tranquillity\Application\Service\Person\UpdatePersonRequest;
use Tranquillity\Application\Service\Person\UpdatePersonService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Infrastructure\Enum\ResourceTypeEnum;

class UpdatePersonAction
{
    private UpdatePersonService $service;
    private TransactionalSession $txnSession;

    public function __construct(UpdatePersonService $service, TransactionalSession $txn)
    {
        $this->service = $service;
        $this->txnSession = $txn;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Get data from request
        $id = $request->getAttribute('id');
        $payload = $request->getParsedBody();
        $data = $payload['data'] ?? array();

        // Validate resource type in request
        if ($data['type'] != ResourceTypeEnum::PERSON) {
            throw new \Exception("Resource type of '" . ResourceTypeEnum::PERSON . "' is required");
        }

        // Build request to update an existing person from payload
        $createPersonRequest = UpdatePersonRequest::createFromArray($id, $data['attributes']);

        // Execute transaction to update person
        $txnService = new TransactionalService($this->service, $this->txnSession);
        $person = $txnService->execute($createPersonRequest, new PersonResourceObjectDataTransformer($request));
        $response->getBody()->write(json_encode($person));
        return $response->withHeader('Content-Type', 'application/vnd.api+json');
    }
}
