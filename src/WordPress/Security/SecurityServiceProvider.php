<?php

declare(strict_types=1);

namespace WPJarvis\Security;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers security-related services like Nonce and Capabilities into the container.
 */
class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register security services in the container.
     *
     * @return void
     */
    public function register(): void
    {
        // Binding as singletons in case future stateful implementations are needed.
        $this->app->singleton('nonce', fn() => new Nonce());
        $this->app->alias('nonce', Nonce::class);

        $this->app->singleton('capabilities', fn() => new Capabilities());
        $this->app->alias('capabilities', Capabilities::class);
    }

    /**
     * Bootstrap security services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Hook into WordPress here if needed (e.g., global permission gates or checks).
    }
}
