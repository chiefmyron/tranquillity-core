<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Responder;

use Psr\Http\Message\ResponseInterface;

class JsonApiResponder
{
    /**
     * Write a JSON:api response
     *
     * @param ResponseInterface $response
     * @param mixed $body
     * @param integer $statusCode
     * @param array $headers
     * @return ResponseInterface
     */
    public static function writeResponse(ResponseInterface $response, $body, int $statusCode, array $headers = []): ResponseInterface
    {
        // Set standard 'Content-Type' header for JSON:api responses
        $response = $response->withHeader('Content-Type', 'application/vnd.api+json');
        foreach ($headers as $key => $value) {
            $response->withHeader($key, $value);
        }

        // Set HTTP status code
        $response = $response->withStatus($statusCode);

        // Write payload body
        $response->getBody()->write(json_encode($body));
        return $response;
    }
}
