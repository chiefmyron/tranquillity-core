<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Action\Auth;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Delivery\RestApi\Action\AbstractAction;

class TokenRequestAction extends AbstractAction
{
    private AuthorizationServer $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Check token request against OAuth server
        try {
            // Attempt to response to the token request
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted as HTTP responses
            // Any other exceptions will be handled by framework error handling middleware
            return $exception->generateHttpResponse($response);
        }
    }
}
