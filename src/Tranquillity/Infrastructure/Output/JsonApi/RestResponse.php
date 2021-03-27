<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Output\JsonApi\Document\AbstractDocument;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataCollectionDocument;
use Tranquillity\Infrastructure\Output\JsonApi\Document\DataDocument;
use Tranquillity\Infrastructure\Output\JsonApi\Document\ErrorDocument;

class RestResponse
{
    private ?AbstractDocument $document;
    private int $httpStatusCode;
    private array $headers;

    public function __construct(?AbstractDocument $document = null, int $httpStatusCode = -1, array $headers = [])
    {
        $this->document = $document;
        $this->httpStatusCode = $httpStatusCode;
        $this->headers = $headers;
    }

    public function setDocument(AbstractDocument $document): void
    {
        $this->document = $document;
    }

    public function setHttpStatusCode(int $httpStatusCode): void
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function replaceHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function writeResponse(ServerRequestInterface $request, ResponseInterface $response, array $options = []): ResponseInterface
    {
        // Always include the JSON:api content type header
        $response = $response->withHeader('Content-Type', 'application/vnd.api+json');

        // Add headers from the response
        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        // Set HTTP status code
        $response = $response->withStatus($this->httpStatusCode);

        // Write payload body
        if (is_null($this->document) == false) {
            $documentClass = get_class($this->document);
            switch ($documentClass) {
                case DataDocument::class:
                    /** @var DataDocument */
                    $document = $this->document;

                    // Render single document response payload
                    $displaySelfLink = $options['displaySelfLink'] ?? true;
                    $body = $document->render($request, $displaySelfLink);
                    break;
                case DataCollectionDocument::class:
                    /** @var DataCollectionDocument */
                    $document = $this->document;

                    // Render collection document response payload
                    $displaySelfLink = $options['displaySelfLink'] ?? true;
                    $displayPaginationLinks = $options['displayPaginationLinks'] ?? true;
                    $body = $document->render($request, $displaySelfLink, $displayPaginationLinks);
                    break;
                case ErrorDocument::class:
                    $body = $this->document->render($request);
                    break;
                default:
                    $body = [];
                    break;
            }
            $response->getBody()->write(json_encode($body));
        }

        return $response;
    }
}
