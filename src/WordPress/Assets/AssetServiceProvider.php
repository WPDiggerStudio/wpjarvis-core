<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Service provider for managing asset registration and enqueueing.
 * Binds asset registrars into the container and hooks into WordPress lifecycle.
 */
class AssetServiceProvider extends ServiceProvider
{
    /**
     * Register core asset services into the container.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind the core registrar for shared or dynamic usage
        $this->app->singleton('assets.registrar', function ($app) {
            return new AssetRegistrar($app);
        });

        // Bind admin asset registrar
        $this->app->singleton('assets.admin', function ($app) {
            return new AdminAssetRegistrar($app);
        });

        // Bind frontend asset registrar
        $this->app->singleton('assets.frontend', function ($app) {
            return new FrontendAssetRegistrar($app);
        });

        // Define class aliases for type-hinting or direct usage
        $this->app->alias('assets.registrar', AssetRegistrar::class);
        $this->app->alias('assets.admin', AdminAssetRegistrar::class);
        $this->app->alias('assets.frontend', FrontendAssetRegistrar::class);
    }

    /**
     * Bootstrap asset-related hooks into the WordPress lifecycle.
     *
     * @return void
     */
    public function boot(): void
    {
        // Register and enqueue admin assets
        add_action('admin_enqueue_scripts', [$this->app['assets.admin'], 'register'], 5);
        add_action('admin_enqueue_scripts', [$this->app['assets.admin'], 'enqueue'], 10);

        // Register and enqueue frontend assets
        add_action('wp_enqueue_scripts', [$this->app['assets.frontend'], 'register'], 5);
        add_action('wp_enqueue_scripts', [$this->app['assets.frontend'], 'enqueue'], 10);
    }
}