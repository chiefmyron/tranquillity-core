<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Error;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

class RestApiErrorHandler implements ErrorHandlerInterface
{
    // Error handler details
    protected string $responseContentType = 'application/vnd.api+json';
    protected ResponseFactoryInterface $responseFactory;
    protected LoggerInterface $logger;
    protected ErrorRendererInterface $responseErrorRenderer;
    protected ErrorRendererInterface $logErrorRenderer;

    // Error instance details
    protected ServerRequestInterface $request;
    protected Throwable $exception;
    protected string $correlationId;
    protected bool $displayErrorDetails;
    protected bool $logErrors;
    protected bool $logErrorDetails;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param LoggerInterface $logger
     * @param ErrorRendererInterface $responseErrorRenderer
     * @param ErrorRendererInterface $logErrorRenderer
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
        ErrorRendererInterface $responseErrorRenderer,
        ErrorRendererInterface $logErrorRenderer
    ) {
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->responseErrorRenderer = $responseErrorRenderer;
        $this->logErrorRenderer = $logErrorRenderer;
    }

    /**
     * Invoke error handler
     *
     * @param ServerRequestInterface $request             The most recent Request object
     * @param Throwable              $exception           The caught Exception object
     * @param bool                   $displayErrorDetails Whether or not to display the error details
     * @param bool                   $logErrors           Whether or not to log errors
     * @param bool                   $logErrorDetails     Whether or not to log error details
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;
        $this->request = $request;
        $this->exception = $exception;

        // Generate correlation ID for this error
        $this->correlationId = Uuid::uuid4()->toString();

        if ($logErrors) {
            $this->writeLogMessage();
        }

        return $this->writeResponse();
    }

    public function setLogErrorRenderer(ErrorRendererInterface $logErrorRenderer)
    {
        $this->logErrorRenderer = $logErrorRenderer;
    }

    public function setResponseErrorRenderer(ErrorRendererInterface $responseErrorRenderer)
    {
        $this->responseErrorRenderer = $responseErrorRenderer;
    }

    /**
     * Write to the error log if $logErrors has been set to true
     *
     * @return void
     */
    protected function writeLogMessage(): void
    {
        // Render log message
        $error = call_user_func($this->logErrorRenderer, $this->exception, $this->logErrorDetails);
        if (!$this->displayErrorDetails) {
            $error .= "\nTips: To display error details in HTTP response ";
            $error .= 'set "displayErrorDetails" to true in the ErrorHandler constructor.';
        }

        // Write message to log
        $this->logger->error($error, ['correlationId' => $this->correlationId]);
    }

    /**
     * @return ResponseInterface
     */
    protected function writeResponse(): ResponseInterface
    {
        // Generate error object / response body
        $error = call_user_func($this->responseErrorRenderer, $this->exception, $this->displayErrorDetails);

        // Enrich error object with correlation ID, and determine HTTP status code to use
        $statusCode = -1;
        $errorArray = json_decode($error, true);
        $errorObjects = &$errorArray['errors'] ?? [];
        foreach ($errorObjects as &$errorObject) {
            // Set correlation ID for error
            $errorObject['id'] = $this->correlationId;

            // Determine whether to use the HTTP status code for this error
            $errorCodeStr = $errorObject['status'];
            if (is_numeric($errorCodeStr) == true) {
                $errorCode = intval($errorCodeStr);
                if ($errorCode > $statusCode) {
                    $statusCode = $errorCode;
                }
            }
        }
        $error = (string) json_encode($errorArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // If error was triggered by an 'OPTIONS' call, override the status code to be OK (HTTP 200)
        if ($this->request->getMethod() === 'OPTIONS') {
            $statusCode = HttpStatusCodeEnum::OK;
        }

        // If error does not contain any HTTP status codes, default to internal server error (HTTP 500)
        if ($statusCode == -1) {
            $statusCode = HttpStatusCodeEnum::INTERNAL_SERVER_ERROR;
        }

        // Generate response
        $response = $this->responseFactory->createResponse($statusCode);
        $response = $response->withHeader('Content-Type', $this->responseContentType);

        // If exception was from a HTTP 405 (Not Allowed) error, include the 'Allow' header
        if ($this->exception instanceof HttpMethodNotAllowedException) {
            $allowedMethods = implode(', ', $this->exception->getAllowedMethods());
            $response = $response->withHeader('Allow', $allowedMethods);
        }

        // Write body of response and return
        $response->getBody()->write($error);
        return $response;
    }
}
