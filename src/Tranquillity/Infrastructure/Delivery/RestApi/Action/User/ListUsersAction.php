<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\ListUsers\ListUsersRequest;
use Tranquillity\Application\Service\ListUsers\ListUsersService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractListAction;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class ListUsersAction extends AbstractListAction
{
    private ListUsersService $service;

    public function __construct(ListUsersService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Build service request
        $listUsersRequest = new ListUsersRequest(
            $this->getFilterParameters($request),
            $this->getSortParameters($request),
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request),
            $this->getPageNumber($request),
            $this->getPageSize($request)
        );

        /** @var RestResponse */
        $users = $this->service->execute($listUsersRequest);
        return $users->writeResponse($request, $response);
    }
}
