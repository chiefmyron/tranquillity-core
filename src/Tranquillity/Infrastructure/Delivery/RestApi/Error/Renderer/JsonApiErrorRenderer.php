<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Error\Renderer;

use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;
use Tranquillity\Domain\Exception\DomainException;
use Tranquillity\Domain\Exception\ValidationException;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;

/**
 * JSONApi error response renderer
 *
 * Error objects are composed of the following members:
 *     id: a unique identifier for this particular occurrence of the problem.
 *     links: a links object containing the following members:
 *         about: a link that leads to further details about this particular occurrence of the problem.
 *     status: the HTTP status code applicable to this problem, expressed as a string value.
 *     code: an application-specific error code, expressed as a string value.
 *     title: a human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence.
 *     detail: a human-readable explanation specific to this occurrence of the problem.
 *     source: an object containing references to the source of the error, optionally including:
 *         pointer: a JSON Pointer [RFC6901] to the associated entity in the request document.
 *         parameter: a string indicating which URI query parameter caused the error.
 *     meta: a meta object containing non-standard meta-information about the error.
 */

class JsonApiErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        // Population of error object depends on what type of exception has been thrown
        $errors = [];
        if ($exception instanceof ValidationException) {
            // Validation error (or collection of errors) from a Domain entity
            $errors = $this->formatValidationException($exception, $displayErrorDetails);
        } elseif ($exception instanceof DomainException) {
            // Business logic error (or collection of errors) from the Domain
            $errors = $this->formatDomainException($exception, $displayErrorDetails);
        } elseif ($exception instanceof HttpException) {
            // Framework HTTP exception
            $errors = $this->formatHttpException($exception, $displayErrorDetails);
        } else {
            // Unexpected application exception
            $errors = $this->formatGenericException($exception, $displayErrorDetails);
        }

        return (string) json_encode(['errors' => $errors], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    private function formatValidationException(ValidationException $exception, bool $displayErrorDetails): array
    {
        // Get the set of notifications held inside the validation exception
        $notification = $exception->getNotification();
        if ($notification->hasItems() == false) {
            return $this->formatGenericException($exception, $displayErrorDetails);
        }

        $errors = [];
        foreach ($notification->getItems() as $item) {
            // Build up error object detail
            $error = [
                'status' => $item->getStatusCode(),
                'title' => $item->getTitle(),
                'code' => $item->getErrorCode(),
            ];

            // Add extra detail if it has been provided
            if ($item->getDetail() != '') {
                $error['detail'] = $item->getDetail();
            }
            if (is_null($item->getSource()) == false) {
                $error['source'] = $item->getSource();
            }

            // If we are displaying error details, include them in the error metadata
            if ($displayErrorDetails == true && count($item->getMeta()) > 0) {
                $error['meta'] = $item->getMeta();
            }

            // Add to error array
            $errors[] = $error;
        }

        return $errors;
    }

    private function formatDomainException(DomainException $exception, bool $displayErrorDetails): array
    {
        // Build up error object detail
        $error = [
            'status' => $exception->getStatusCode(),
            'title' => $exception->getTitle(),
        ];

        // Add extra detail if it has been provided
        if ($exception->getErrorCode() != '') {
            $error['code'] = $exception->getErrorCode();
        }
        if ($exception->getDetail() != '') {
            $error['detail'] = $exception->getDetail();
        }
        if (is_null($exception->getSource()) == false) {
            $error['source'] = $exception->getSource();
        }

        // If we are displaying error details, include them in the error metadata
        if ($displayErrorDetails == true) {
            $error['meta'] = [];
            $error['meta']['exception'] = $this->formatExceptionDetails($exception);
        }

        // JSON:api error responses should always be an array of error objects
        $errors = [];
        $errors[] = $error;
        return $errors;
    }

    private function formatHttpException(HttpException $exception, bool $displayErrorDetails): array
    {
        // Build up error object detail
        $error = [
            'status' => $exception->getCode(), // The HTTP status code applicable to this problem
            'title'  => $exception->getTitle(),
            'detail' => $exception->getMessage(),
        ];

        // If we are displaying error details, include them in the error metadata
        if ($displayErrorDetails == true) {
            $error['meta'] = [];
            $error['meta']['exception'] = $this->formatExceptionDetails($exception);
        }

        // JSON:api error responses should always be an array of error objects
        $errors = [];
        $errors[] = $error;
        return $errors;
    }

    private function formatGenericException(Throwable $exception, bool $displayErrorDetails): array
    {
        // Build up error object detail
        $error = [
            'status' => HttpStatusCodeEnum::INTERNAL_SERVER_ERROR,
            'title' => 'Internal server error',
            'detail' => $exception->getMessage()
        ];

        // If we are displaying error details, include them in the error metadata
        if ($displayErrorDetails == true) {
            $error['meta'] = [];
            $error['meta']['exception'] = $this->formatExceptionDetails($exception);
        }

        // JSON:api error responses should always be an array of error objects
        $errors = [];
        $errors[] = $error;
        return $errors;
    }

    private function formatExceptionDetails(Throwable $exception): array
    {
        $exceptionDetails = [];
        do {
            $exceptionDetail = [
                'type' => get_class($exception),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace()
            ];
            $exceptionDetails[] = $exceptionDetail;
        } while ($exception = $exception->getPrevious());
        return $exceptionDetail;
    }
}
