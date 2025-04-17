<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * Class Queue
 *
 * Facade for interacting with the queue dispatcher.
 *
 * @method static mixed dispatch(object $job)
 *
 * @see \WPJarvis\Core\Queue\Dispatcher
 */
class Queue extends Facade
{
    /**
     * Get the underlying service name.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'queue.dispatcher';
    }
}
