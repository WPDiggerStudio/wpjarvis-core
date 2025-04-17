<?php

declare(strict_types=1);

namespace WPJarvis\Core\Queue;

use Illuminate\Contracts\Bus\Dispatcher as IlluminateDispatcher;

/**
 * Class Dispatcher
 *
 * Wrapper around Laravel's Bus Dispatcher to allow easy job dispatching.
 *
 * @package WPJarvis\Core\Queue
 */
class Dispatcher
{
    /**
     * Laravel's dispatcher instance.
     *
     * @var IlluminateDispatcher
     */
    protected IlluminateDispatcher $dispatcher;

    /**
     * Constructor.
     *
     * @param IlluminateDispatcher $dispatcher
     */
    public function __construct(IlluminateDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch the given job.
     *
     * @param mixed $job
     * @return mixed
     */
    public function dispatch(mixed $job): mixed
    {
        return $this->dispatcher->dispatch($job);
    }
}
