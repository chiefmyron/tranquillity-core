<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Middleware;

// PSR standards interfaces
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tranquillity\Infrastructure\Profiling\Profiler;

final class ProfilerMiddleware implements MiddlewareInterface
{
    /**
     * @var Profiler
     */
    private $profiler;

    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
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
        // Run regular application logic first
        $response = $handler->handle($request);

        // If the profiler is enabled, run data collection and update response
        $profile = $this->profiler->collect($request, $response);
        if ($profile !== null) {
            $this->profiler->saveProfile($profile);

            // Add profile token to the response as metadata
            $bodyJson = (string)$response->getBody();
            $body = json_decode($bodyJson, true);
            if (isset($body['meta']) == false) {
                $body['meta'] = [];
            }
            $body['meta']['TQL_PROFILE_TOKEN'] = $profile->getToken();

            // Write back new response body
            $response->getBody()->rewind();
            $response->getBody()->write(json_encode($body));
        }

        return $response;
    }
}
