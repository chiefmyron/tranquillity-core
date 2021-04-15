<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Middleware;

use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private ResourceServer $server;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResourceServer $server, ResponseFactoryInterface $responseFactory)
    {
        $this->server = $server;
        $this->responseFactory = $responseFactory;
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
        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            $response = $this->responseFactory->createResponse();
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $response = $this->responseFactory->createResponse();
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);
        }

        // Pass the request and response on to the next responder in the chain
        $response = $handler->handle($request);
        return $response;
    }
}
