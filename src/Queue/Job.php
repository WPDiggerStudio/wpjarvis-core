<?php

declare(strict_types=1);

namespace WPJarvis\Core\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class Job
 *
 * Base class for all queueable jobs.
 * Extend this to define jobs that can be dispatched to a queue.
 *
 * @package WPJarvis\Core\Queue
 */
abstract class Job implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Execute the job logic.
     *
     * All child jobs must define how they should be handled.
     *
     * @return void
     */
    abstract public function handle(): void;
}
