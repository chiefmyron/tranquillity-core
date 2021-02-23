<?php

declare(strict_types=1);

namespace Tranquillity\Domain\Event;

class DomainEventPublisher
{
    /**
     * @var array<DomainEventSubscriber>
     */
    private array $subscribers = [];
    private static ?DomainEventPublisher $instance = null;

    public static function instance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public function subscribe(DomainEventSubscriber $eventSubscriber): void
    {
        $this->subscribers[] = $eventSubscriber;
    }

    public function publish(DomainEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            if ($subscriber->isSubscribedTo($event)) {
                $subscriber->handle($event);
            }
        }
    }
}
