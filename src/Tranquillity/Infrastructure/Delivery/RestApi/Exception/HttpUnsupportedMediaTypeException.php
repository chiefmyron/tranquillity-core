<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpUnsupportedMediaTypeException extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 415;

    /**
     * @var string
     */
    protected $message = 'Unsupported media type.';

    protected $title = '415 Unsupported Media Type';
    protected $description = 'The media format of the requested data is not supported by the server, so the server is rejecting the request.';
}
