<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\CreatePerson\CreatePersonRequest;
use Tranquillity\Application\Service\CreatePerson\CreatePersonService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class CreatePersonAction extends AbstractAction
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
        if ($data['type'] != EntityTypeEnum::PERSON) {
            throw new \Exception("Resource type of '" . EntityTypeEnum::PERSON . "' is required");
        }

        // Build request to create new person from payload
        $createPersonRequest = CreatePersonRequest::createFromArray(
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request),
            $data['attributes']
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->service, $this->txnSession);

        /** @var RestResponse */
        $person = $txnService->execute($createPersonRequest);
        return $person->writeResponse($request, $response);
    }
}
