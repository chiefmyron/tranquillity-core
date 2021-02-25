<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\Person\ListPeopleRequest;
use Tranquillity\Application\Service\Person\ListPeopleService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractListAction;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApiPersonCollectionDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class ListPeopleAction extends AbstractListAction
{
    private ListPeopleService $service;

    public function __construct(ListPeopleService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $listPeopleRequest = new ListPeopleRequest(
            $this->getFilterParameters($request),
            $this->getSortParameters($request),
            $this->getPageNumber($request),
            $this->getPageSize($request)
        );

        $people = $this->service->execute($listPeopleRequest, new JsonApiPersonCollectionDataTransformer($request));
        return JsonApiResponder::writeResponse($response, $people, HttpStatusCodeEnum::OK);
    }
}
