<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\User\UpdateUserRequest;
use Tranquillity\Application\Service\User\UpdateUserService;
use Tranquillity\Application\Service\TransactionalService;
use Tranquillity\Application\Service\TransactionalSession;
use Tranquillity\Domain\Enum\EntityTypeEnum;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class UpdateUserAction extends AbstractAction
{
    private UpdateUserService $service;
    private TransactionalSession $txnSession;

    public function __construct(UpdateUserService $service, TransactionalSession $txn)
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
        if ($data['type'] != EntityTypeEnum::USER) {
            throw new \Exception("Resource type of '" . EntityTypeEnum::USER . "' is required");
        }

        // Build request to update an existing User from payload
        $updateUserRequest = UpdateUserRequest::createFromArray(
            $id,
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request),
            $data['attributes']
        );

        // Execute as a transaction
        $txnService = new TransactionalService($this->service, $this->txnSession);

        /** @var RestResponse */
        $user = $txnService->execute($updateUserRequest);
        return $user->writeResponse($request, $response);
    }
}
