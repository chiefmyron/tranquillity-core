<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\Person\UpdatePersonRequest;
use Tranquillity\Application\Service\Person\UpdatePersonService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApiPersonDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class UpdatePersonAction extends AbstractAction
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
        if ($data['type'] != EntityTypeEnum::PERSON) {
            throw new \Exception("Resource type of '" . EntityTypeEnum::PERSON . "' is required");
        }

        // Build request to update an existing person from payload
        $createPersonRequest = UpdatePersonRequest::createFromArray(
            $id,
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request),
            $data['attributes']
        );

        // Execute transaction to update person
        $txnService = new TransactionalService($this->service, $this->txnSession);
        $person = $txnService->execute($createPersonRequest);
        return JsonApiResponder::writeResponse($request, $response, $person, HttpStatusCodeEnum::OK);
    }
}
