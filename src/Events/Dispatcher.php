<?php

declare(strict_types=1);

namespace WPJarvis\Core\Events;

/**
 * Class Dispatcher
 *
 * A Laravel-style event dispatcher that also integrates with WordPress actions.
 *
 * @package WPJarvis\Core\Events
 */
class Dispatcher
{
    /**
     * Array of registered event listeners.
     *
     * @var array<string, array<int, callable>>
     */
    protected array $listeners = [];

    /**
     * Register an event listener.
     *
     * @param string $event The event name.
     * @param callable $listener The listener callback.
     * @return void
     */
    public function listen(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    /**
     * Determine if there are listeners for a given event.
     *
     * @param string $event
     * @return bool
     */
    public function hasListeners(string $event): bool
    {
        return isset($this->listeners[$event]) && count($this->listeners[$event]) > 0;
    }

    /**
     * Dispatch an event and call all listeners.
     *
     * @param string $event
     * @param mixed ...$payload
     * @return array<int, mixed> Array of responses from listeners.
     */
    public function dispatch(string $event, ...$payload): array
    {
        $responses = [];

        // Fire a WordPress action
        do_action("wpjarvis.event.{$event}", ...$payload);

        // Dispatch custom registered listeners
        if ($this->hasListeners($event)) {
            foreach ($this->listeners[$event] as $listener) {
                $responses[] = call_user_func_array($listener, $payload);
            }
        }

        return $responses;
    }

    /**
     * Dispatch an event until a non-null response is returned.
     *
     * @param string $event
     * @param mixed ...$payload
     * @return mixed|null
     */
    public function dispatchUntil(string $event, ...$payload): mixed
    {
        if ($this->hasListeners($event)) {
            foreach ($this->listeners[$event] as $listener) {
                $response = call_user_func_array($listener, $payload);
                if ($response !== null) {
                    return $response;
                }
            }
        }

        return null;
    }

    /**
     * Remove all listeners for a specific event.
     *
     * @param string $event
     * @return void
     */
    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * Clear all event listeners.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->listeners = [];
    }

    /**
     * Get all listeners for a given event.
     *
     * @param string $event
     * @return array<int, callable>
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
