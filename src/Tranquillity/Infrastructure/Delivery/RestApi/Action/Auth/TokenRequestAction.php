<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Auth;

use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;

class TokenRequestAction extends AbstractAction
{
    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Check token request against OAuth server
        $serverRequest = Request::createFromGlobals();

        /** @var Response */
        $serverResponse = $this->server->handleTokenRequest($serverRequest);

        // Send back response
        $response = $response->withStatus($serverResponse->getStatusCode());
        foreach ($serverResponse->getHttpHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($serverResponse->getResponseBody('json'));
        return $response;
    }
}
