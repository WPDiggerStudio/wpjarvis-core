<?php

declare(strict_types=1);

namespace WPJarvis\Core\Foundation\Bootstrap;

use WPJarvis\Core\Foundation\Application;

/**
 * Class BootProviders
 *
 * This bootstrapper is responsible for booting all registered service providers.
 * It runs after providers have been registered and configuration is loaded.
 *
 * @package WPJarvis\Core\Foundation\Bootstrap
 */
class BootProviders
{
    /**
     * Bootstrap the service providers for the application.
     *
     * This is the final step in the application bootstrap sequence,
     * allowing providers to execute any logic that requires the
     * entire application to be registered.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        // Hook before booting providers (can be used in future for lifecycle events)
        do_action('wpjarvis.booting');

        // Boot all registered providers
        $app->boot();

        // Hook after all providers are booted
        do_action('wpjarvis.booted');
    }
}
