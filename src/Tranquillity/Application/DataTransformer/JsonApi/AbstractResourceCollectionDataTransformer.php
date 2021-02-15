<?php

declare(strict_types=1);

namespace Tranquillity\Application\DataTransformer\JsonApi;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractResourceCollectionDataTransformer
{
    protected ServerRequestInterface $request;

    protected array $data = [];
    protected array $included = [];
    protected array $links = [];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function read(): array
    {
        return [
            'links' => $this->links,
            'data' => $this->data,
            'included' => $this->included
        ];
    }
}
