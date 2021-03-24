<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Output\JsonApi\Document;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractDocument
{
    protected array $meta = [];
    protected array $links = [];
    protected array $included = [];
    protected string $jsonApiVersion = '1.0';

    public function render(ServerRequestInterface $request): array
    {
        // Build return array
        $result = [];
        if (count($this->links) > 0) {
            $result['links'] = $this->links;
        }
        if (count($this->included) > 0) {
            $result['included'] = $this->included;
        }
        if (count($this->meta) > 0) {
            $result['meta'] = $this->meta;
        }
        $result['jsonapi'] = ['version' => $this->jsonApiVersion];
        return $result;
    }

    public function addLink(string $name, ?string $url, array $meta = []): void
    {
        if (count($meta) <= 0) {
            $this->links[$name] = $url;
        } else {
            $this->links[$name] = [
                'href' => $url,
                'meta' => $meta
            ];
        }
    }

    public function addMeta(string $name, $value): void
    {
        $this->meta[$name] = $value;
    }
}
