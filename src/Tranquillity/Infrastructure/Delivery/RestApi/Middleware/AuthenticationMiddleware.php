<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Middleware;

use OAuth2\Server as OAuthServer;
use OAuth2\Request as OAuthRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private OAuthServer $server;

    public function __construct(OAuthServer $server)
    {
        $this->server = $server;
    }

    /**
     * Invoke middleware functionality
     *
     * @param ServerRequestInterface $request PSR-7 HTTP request
     * @param RequestHandlerInterface $handler PSR-7 HTTP request handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = RouteContext::fromRequest($request)->getRoute();
        $args = $route->getArguments();
        $scope = $args['auth-scope'] ?? '';

        // Authenticate request
        $req = OAuthRequest::createFromGlobals();
        if ($this->server->verifyResourceRequest($req, $this->server->getResponse(), $scope) != true) {
            $this->server->getResponse()->send();
            exit();
        }

        // Store the username for the authenticated user in the request
        $token = $this->server->getAccessTokenData($req);
        $tokenUserId = $token['user_id'] ?? null;
        $tokenClientName = $token['client_id'] ?? 'invalid_client_id';

        // Update request with audit trail information in the 'meta' section
        $body = $request->getParsedBody();
        $meta = $body['meta'] ?? [];
        $meta['user'] = $tokenUserId;
        $meta['client'] = $tokenClientName;
        $body['meta'] = $meta;
        $request = $request->withParsedBody($body);

        // Run regular application logic first
        $response = $handler->handle($request);
        return $response;
    }
}
