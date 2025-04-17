<?php

declare(strict_types=1);

namespace WPJarvis\Core\Foundation\Bootstrap;

use WPJarvis\Core\Foundation\Application;

/**
 * Class RegisterProviders
 *
 * Responsible for registering all configured service providers into the application container.
 * This includes core, package, and plugin-level providers listed in the config/app.php file.
 *
 * @package WPJarvis\Core\Foundation\Bootstrap
 */
class RegisterProviders
{
    /**
     * Bootstrap the registration of service providers.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        // Fire pre-registration hook for developers
        do_action('wpjarvis.providers.registering');

        // Retrieve the provider list from config/app.php
        $providers = $app->make('config')->get('app.providers', []);

        foreach ($providers as $provider) {
            // You may optionally skip registration via a filter
            if (!apply_filters('wpjarvis.providers.should_register', true, $provider)) {
                continue;
            }

            $app->register($provider);
        }

        // Fire post-registration hook
        do_action('wpjarvis.providers.registered', $providers);
    }
}
