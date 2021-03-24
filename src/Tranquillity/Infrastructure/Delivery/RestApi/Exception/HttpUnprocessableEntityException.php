<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpUnprocessableEntityException extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @var string
     */
    protected $message = 'Unprocessable entity.';

    protected $title = '422 Unprocessable Entity';
    protected $description = 'The request was well-formed but was unable to be followed due to semantic errors.';
}
