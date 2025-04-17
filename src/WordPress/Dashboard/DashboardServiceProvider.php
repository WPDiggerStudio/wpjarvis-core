<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Dashboard;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers the dashboard widget system with WordPress via the container.
 */
class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register the dashboard widget registrar as a singleton in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('dashboard.registrar', function ($app) {
            return new WidgetRegistrar($app);
        });

        // Provide a class-based alias for easier resolution and type-hinting
        $this->app->alias('dashboard.registrar', WidgetRegistrar::class);
    }

    /**
     * Hook into WordPress to register dashboard widgets during setup.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('wp_dashboard_setup', [$this->app['dashboard.registrar'], 'register']);
    }
}
