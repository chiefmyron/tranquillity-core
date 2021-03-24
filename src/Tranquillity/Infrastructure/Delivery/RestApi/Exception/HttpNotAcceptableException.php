<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpNotAcceptableException extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 406;

    /**
     * @var string
     */
    protected $message = 'Not acceptable.';

    protected $title = '406 Not Acceptable';
    protected $description = 'This response is sent when the web server, after performing server-driven content negotiation, doesn\'t find any content that conforms to the criteria given by the user agent.';
}
