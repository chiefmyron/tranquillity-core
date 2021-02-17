<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\DataTransformer\JsonApi\PersonResourceObjectDataTransformer;
use Tranquillity\Application\Service\Person\ViewPersonRequest;
use Tranquillity\Application\Service\Person\ViewPersonService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractListAction;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class ViewPersonAction extends AbstractListAction
{
    private ViewPersonService $service;

    public function __construct(ViewPersonService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $request->getAttribute('id');
        $fields = $request->getAttribute('fields') ?? [];
        $relatedResources = $request->getAttribute('include') ?? [];

        $viewPersonRequest = new ViewPersonRequest($id, $fields, $relatedResources);
        $person = $this->service->execute($viewPersonRequest, new PersonResourceObjectDataTransformer($request));

        $response->getBody()->write(json_encode($person));
        $response = $response->withStatus(HttpStatusCodeEnum::OK);
        return $response->withHeader('Content-Type', 'application/vnd.api+json');
    }
}
