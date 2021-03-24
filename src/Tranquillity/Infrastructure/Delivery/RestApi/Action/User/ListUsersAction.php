<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\User\ListUsersRequest;
use Tranquillity\Application\Service\User\ListUsersService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractListAction;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\User\JsonApiUserCollectionDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

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

        $people = $this->service->execute($listUsersRequest);
        return JsonApiResponder::writeResponse($request, $response, $people, HttpStatusCodeEnum::OK);
    }
}
