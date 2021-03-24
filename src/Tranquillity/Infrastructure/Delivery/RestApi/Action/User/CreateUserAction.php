<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\User\CreateUserRequest;
use Tranquillity\Application\Service\User\CreateUserService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

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

        // Execute transaction to create new User
        $txnService = new TransactionalService($this->service, $this->txnSession);
        $user = $txnService->execute($createUserRequest);
        return JsonApiResponder::writeResponse($request, $response, $user, HttpStatusCodeEnum::CREATED);
    }
}
