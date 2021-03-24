<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Middleware;

// PSR standards interfaces

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpBadRequestException;
use Tranquillity\Infrastructure\Delivery\RestApi\Exception\HttpNotAcceptableException;
use Tranquillity\Infrastructure\Delivery\RestApi\Exception\HttpUnprocessableEntityException;
use Tranquillity\Infrastructure\Delivery\RestApi\Exception\HttpUnsupportedMediaTypeException;

final class JsonApiRequestValidationMiddleware implements MiddlewareInterface
{
    private const JSONAPI_CONTENT_TYPE = 'application/vnd.api+json';
    private const JSONAPI_PAYLOAD_METHODS = ['POST', 'PUT', 'DELETE'];

    /**
     * Invoke middleware functionality
     *
     * @param ServerRequestInterface $request PSR-7 HTTP request
     * @param RequestHandlerInterface $handler PSR-7 HTTP request handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Check content type header
        if (in_array(self::JSONAPI_CONTENT_TYPE, $request->getHeader('Content-Type')) == false) {
            throw new HttpUnsupportedMediaTypeException($request, "Content-Type for API requests must be '" . self::JSONAPI_CONTENT_TYPE . "'");
        }

        // Check accept header
        if ($request->hasHeader('Accept') == true && in_array(self::JSONAPI_CONTENT_TYPE, $request->getHeader('Content-Type')) == false) {
            throw new HttpNotAcceptableException($request, "If provided in an 'Accept' header, the '" . self::JSONAPI_CONTENT_TYPE . "' media type must be present at least once without media type parameters");
        }

        // Check request method
        $payloadMethods = self::JSONAPI_PAYLOAD_METHODS;
        if (in_array($request->getMethod(), $payloadMethods) == true) {
            // Validate payload structure
            $body = $request->getParsedBody();
            if ($body == null) {
                throw new HttpBadRequestException($request, "Unable to parse request body into JSON");
            }

            // Check for top-level document members
            if (array_key_exists('data', $body) == false && array_key_exists('errors', $body) == false && array_key_exists('meta', $body) == false) {
                throw new HttpUnprocessableEntityException($request, "A document MUST contain at least one of the following top-level members: data, errors, meta");
            }
        }

        // Run regular application with validated request
        $response = $handler->handle($request);
        return $response;
    }
}
