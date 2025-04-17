<?php

declare(strict_types=1);

namespace WPJarvis\Core\Events;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * Registers the event dispatcher in the application container.
 *
 * @package WPJarvis\Core\Events
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('events', function () {
            return new Dispatcher();
        });
    }

    /**
     * Bootstrap services after registration.
     *
     * @return void
     */
    public function boot(): void
    {
        // You can bind subscribers or trigger initial events here in future.
    }
}
