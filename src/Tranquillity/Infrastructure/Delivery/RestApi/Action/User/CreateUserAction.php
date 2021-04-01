<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\CreateUser\CreateUserRequest;
use Tranquillity\Application\Service\CreateUser\CreateUserService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class CreateUserAction
{
    private CreateUserService $service;
    private TransactionalSession $txnSession;

    public function __construct(CreateUserService $service, TransactionalSession $txn)
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
        if ($data['type'] != EntityTypeEnum::USER) {
            throw new \DomainException("Resource type of '" . EntityTypeEnum::USER . "' is required");
        }

        // Build request to create new User from payload
        $createUserRequest = CreateUserRequest::createFromArray($data['attributes']);

        // Execute as a transaction
        $txnService = new TransactionalService($this->service, $this->txnSession);

        /** @var RestResponse */
        $user = $txnService->execute($createUserRequest);
        return $user->writeResponse($request, $response);
    }
}
