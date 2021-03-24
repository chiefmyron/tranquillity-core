<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\Document;

use Psr\Http\Message\ServerRequestInterface;
use Tranquillity\Infrastructure\Output\JsonApi\ResourceObject\AbstractResourceObject;

class DataDocument extends AbstractDocument
{
    protected AbstractResourceObject $data;

    public function __construct(AbstractResourceObject $data)
    {
        $this->data = $data;
    }

    public function render(ServerRequestInterface $request, bool $includeSelfLink = true): array
    {
        // Include 'self' as a top-level link
        if ($includeSelfLink == true && $request->getMethod() == 'GET') {
            $this->addLink('self', (string)$request->getUri());
        }

        // Get common members
        $result = parent::render($request);

        // Add data element
        $result['data'] = $this->data->render();

        // Return completed document body
        return $result;
    }
}
