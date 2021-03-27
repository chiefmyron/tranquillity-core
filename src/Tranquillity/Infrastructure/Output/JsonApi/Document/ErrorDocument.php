<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\Document;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Enum\HttpStatusCodeEnum;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\ErrorObject;

class ErrorDocument extends AbstractDocument
{
    protected array $errors;
    protected int $httpStatusCode;

    /**
     * Constructor
     *
     * @param array<ErrorObject> $errors
     * @param integer $httpStatusCode
     */
    public function __construct(array $errors, int $httpStatusCode = -1)
    {
        // Set initial HTTP status code for the error collection
        $this->httpStatusCode = $httpStatusCode;

        // Parse the error collection
        $this->errors = [];
        $errorHttpStatusCode = -1;
        foreach ($errors as $error) {
            // Add to error array
            $this->errors[] = $error->render();

            // Check the HTTP status to use for the error
            if ($error->getHttpStatusCode() > $errorHttpStatusCode) {
                $httpStatusCode = $error->getHttpStatusCode();
            }
        }

        // Set the HTTP status code to use for the error response
        if ($httpStatusCode > -1) {
            $this->httpStatusCode = $httpStatusCode;
        } elseif ($errorHttpStatusCode > -1) {
            $this->httpStatusCode = $errorHttpStatusCode;
        } else {
            $this->httpStatusCode = HttpStatusCodeEnum::INTERNAL_SERVER_ERROR;
        }
    }

    public function render(ServerRequestInterface $request, bool $includeSelfLink = true): array
    {
        return ['errors' => $this->errors];
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}
