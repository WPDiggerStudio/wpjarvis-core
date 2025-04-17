<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static void listen(string $event, callable $listener)
 * @method static bool hasListeners(string $event)
 * @method static array dispatch(string $event, mixed ...$payload)
 * @method static mixed dispatchUntil(string $event, mixed ...$payload)
 * @method static void forget(string $event)
 * @method static void flush()
 *
 * @see \WPJarvis\Core\Events\Dispatcher
 */
class Event extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'events';
    }
}
