<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Error\Renderer;

use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;
use Tranquillity\Domain\Validation\ValidationException;
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
 *     title: a short, human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence of the problem.
 *     detail: a human-readable explanation specific to this occurrence of the problem. Like title, this field’s value can be localized.
 *     source: an object containing references to the source of the error, optionally including any of the following members:
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
            // Business logic error (or collection of errors) from the Domain
            $errors = $this->formatValidationException($exception, $displayErrorDetails);
        } elseif ($exception instanceof HttpException) {
            // Framework HTTP exception
            $errors = $this->formatHttpException($exception, $displayErrorDetails);
        } else {
            // Unexpected application exception
            $errors = $this->formatGenericException($exception, $displayErrorDetails);
        }

        return (string) json_encode(['errors' => $errors], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function formatValidationException(ValidationException $exception, bool $displayErrorDetails): array
    {
        return [
            'links' => [          // A links object containing the 'about' member
                'about' => ''     // A link that leads to further detail about this particular occurrence of the problem
            ],
            'status' => '',       // The HTTP status code applicable to this problem (expressed as a string value)
            'code'   => '',       // An application-specific error code, expressed as a string value
            'title'  => '',       // A short, human-readable summary of the problem that should not change from occurrence to occurrence of the problem
            'detail' => '',       // A human-readable explanation specific to this occurrence of the problem
            'source' => [         // An object containing references to the source of the error (either 'pointer' or 'parameter')
                'pointer' => '',  // A JSON Pointer (RFC6901) to the associated entity in the request document
                'parameter' => '' // A string indicating which URI query parameter caused the error
            ],
            'meta' => []          // A meta object containing non-standard information about the error
        ];
    }

    private function formatHttpException(HttpException $exception, bool $displayErrorDetails): array
    {
        // Build up error object detail
        $error = [
            'id' => '',           // A unique identifier for the particular occurrence
            'links' => [          // A links object containing the 'about' member
                'about' => ''     // A link that leads to further detail about this particular occurrence of the problem
            ],
            'status' => $exception->getCode(), // The HTTP status code applicable to this problem
            'title'  => $exception->getTitle(),
            'detail' => $exception->getDescription(),
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
        $exceptionDetail = [];
        do {
            $exceptionDetail[] = [
                'type' => get_class($exception),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ];
        } while ($exception = $exception->getPrevious());
        return $exceptionDetail;
    }
}
