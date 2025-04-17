<?php

declare(strict_types=1);

namespace WPJarvis\Core\Queue;

use WPJarvis\Core\Support\ServiceProvider;
use Illuminate\Queue\Capsule\Manager as QueueCapsule;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Bus\Dispatcher as LaravelDispatcher;

/**
 * Class QueueServiceProvider
 *
 * Registers the Laravel queue system into the WPJarvis container.
 *
 * @package WPJarvis\Core\Queue
 */
class QueueServiceProvider extends ServiceProvider
{
    /**
     * Register queue services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('queue', function ($app) {
            $capsule = new QueueCapsule($app);
            $capsule->addConnection($app->make('config')->get('queue.connections', []));
            return $capsule->getQueueManager();
        });

        $this->app->singleton(BusDispatcher::class, function ($app) {
            return new LaravelDispatcher($app);
        });

        $this->app->singleton('queue.dispatcher', function ($app) {
            return new Dispatcher($app->make(BusDispatcher::class));
        });
    }

    /**
     * Boot queue services (if needed).
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Define the services provided.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['queue', 'queue.dispatcher', BusDispatcher::class];
    }
}
