<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Taxonomy;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers the TaxonomyRegistrar and hooks it into the WordPress lifecycle.
 */
class TaxonomyServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('taxonomy.registrar', function ($app) {
            return new TaxonomyRegistrar($app);
        });

        $this->app->alias('taxonomy.registrar', TaxonomyRegistrar::class);
    }

    /**
     * Hook taxonomy registration into the WordPress init action.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('init', [$this->app['taxonomy.registrar'], 'register']);
    }
}
