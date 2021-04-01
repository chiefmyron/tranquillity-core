<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Application\Service\ViewUser\ViewUserRequest;
use Tranquillity\Application\Service\ViewUser\ViewUserService;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;
use Tranquillity\Infrastructure\Output\JsonApi\RestResponse;

class ViewUserAction extends AbstractAction
{
    private ViewUserService $service;

    public function __construct(ViewUserService $service)
    {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Build service request
        $viewUserRequest = new ViewUserRequest(
            $request->getAttribute('id'),
            $this->getSparseFieldset($request),
            $this->getIncludedResources($request)
        );

        /** @var RestResponse */
        $user = $this->service->execute($viewUserRequest);
        return $user->writeResponse($request, $response);
    }
}
