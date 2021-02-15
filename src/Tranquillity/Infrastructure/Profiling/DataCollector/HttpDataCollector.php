<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Profiling\DataCollector;

use Throwable;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'http';
    }

    /**
     * {@inheritDoc}
     */
    public function collect(ServerRequestInterface $request, ResponseInterface $response, ?Throwable $exception = null)
    {
        // Set request and response details
        $this->data = [
            'request_method' => $request->getMethod(),
            'request_body' => $request->getParsedBody(),
            'request_query' => $request->getQueryParams(),
            'request_files' => $request->getUploadedFiles(),
            'request_headers' => $request->getHeaders(),
            'request_server' => $request->getServerParams(),
            'request_cookies' => $request->getCookieParams(),
            'request_attributes' => $request->getAttributes(),
            'request_target' => $request->getRequestTarget(),
            'response_content_type' => $response->getHeader('Content-Type'),
            'response_status_code' => $response->getStatusCode(),
            'response_status_text' => $response->getReasonPhrase(),
            'response_headers' => $response->getHeaders()
        ];
    }

    /**
     * @inheritdoc
     */
    public function lateCollect()
    {
        // Transform collected data into an array that can be serialised
        $this->data = $this->cloneVar($this->data);
    }
}
