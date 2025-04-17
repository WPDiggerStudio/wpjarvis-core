<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Block;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Service provider for registering Gutenberg blocks.
 * Binds the block registrar to the container and hooks it into WordPress.
 */
class BlockServiceProvider extends ServiceProvider
{
    /**
     * Register the block registrar in the service container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('block.registrar', function ($app) {
            return new BlockRegistrar($app);
        });

        // Optional alias for convenience and type-hinting
        $this->app->alias('block.registrar', BlockRegistrar::class);
    }

    /**
     * Bootstrap the block system by hooking into WordPress.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register blocks during the WordPress init phase
        add_action('init', [$this->app['block.registrar'], 'register']);
    }
}
