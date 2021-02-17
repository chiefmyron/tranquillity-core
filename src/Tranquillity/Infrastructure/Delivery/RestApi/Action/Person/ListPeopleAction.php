<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\Person\ListPeopleRequest;
use Tranquillity\Application\Service\Person\ListPeopleService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractListAction;
use Tranquillity\Application\DataTransformer\JsonApi\PersonResourceCollectionDataTransformer;
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

        $people = $this->service->execute($listPeopleRequest, new PersonResourceCollectionDataTransformer($request));
        $response->getBody()->write(json_encode($people));
        $response = $response->withStatus(HttpStatusCodeEnum::OK);
        return $response->withHeader('Content-Type', 'application/vnd.api+json');
    }
}
