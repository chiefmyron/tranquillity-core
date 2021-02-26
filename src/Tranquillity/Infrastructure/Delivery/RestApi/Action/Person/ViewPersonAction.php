<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Person;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\Person\ViewPersonRequest;
use Tranquillity\Application\Service\Person\ViewPersonService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;
use Tranquillity\Infrastructure\Delivery\RestApi\DataTransformer\Person\JsonApiPersonDataTransformer;
use Tranquillity\Infrastructure\Delivery\RestApi\Responder\JsonApiResponder;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class ViewPersonAction extends AbstractAction
{
    private ViewPersonService $service;

    public function __construct(ViewPersonService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Build service request
        $viewPersonRequest = new ViewPersonRequest(
            $request->getAttribute('id'),
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request)
        );

        $person = $this->service->execute($viewPersonRequest, new JsonApiPersonDataTransformer($request));
        return JsonApiResponder::writeResponse($response, $person, HttpStatusCodeEnum::OK);
    }
}
