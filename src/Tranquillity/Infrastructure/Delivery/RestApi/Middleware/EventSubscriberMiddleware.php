<?php

declare(strict_types=1);

namespace Tranquillity\Infrastructure\Delivery\RestApi\Middleware;

// PSR standards interfaces

use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tranquillity\Domain\Event\DomainEventPublisher;
use Tranquillity\Domain\Event\DomainEventStore;
use Tranquillity\Infrastructure\Domain\Event\Doctrine\DoctrineEventSubscriber;

final class EventSubscriberMiddleware implements MiddlewareInterface
{
    private DomainEventPublisher $publisher;
    private DomainEventStore $eventStore;

    public function __construct(DomainEventPublisher $publisher, DomainEventStore $eventStore)
    {
        $this->publisher = $publisher;
        $this->eventStore = $eventStore;
    }

    /**
     * Invoke middleware functionality
     *
     * @param ServerRequestInterface $request PSR-7 HTTP request
     * @param RequestHandlerInterface $handler PSR-7 HTTP request handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Register event subscribers
        $this->publisher->subscribe(new DoctrineEventSubscriber($this->eventStore));

        // Run regular application logic first
        $response = $handler->handle($request);
        return $response;
    }
}
