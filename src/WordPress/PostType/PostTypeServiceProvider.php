<?php
declare(strict_types=1);

namespace WPJarvis\Core\WordPress\PostType;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Binds the PostTypeRegistrar into the container and triggers registration.
 */
class PostTypeServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('posttype.registrar', function ($app) {
            return new PostTypeRegistrar($app);
        });

        $this->app->alias('posttype.registrar', PostTypeRegistrar::class);
    }

    /**
     * Bootstrap the registrar into the WordPress lifecycle.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('init', [$this->app['posttype.registrar'], 'register']);
    }
}
