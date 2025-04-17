<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Metabox;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Service provider for registering WordPress metaboxes using the container.
 */
class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * Register the metabox registrar in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('metabox.registrar', function ($app) {
            return new MetaboxRegistrar($app);
        });

        $this->app->alias('metabox.registrar', MetaboxRegistrar::class);
    }

    /**
     * Bootstrap the metabox system by hooking into WordPress.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('add_meta_boxes', [$this->app['metabox.registrar'], 'registerMetaboxes']);
        add_action('save_post', [$this->app['metabox.registrar'], 'saveMetaboxes']);
    }
}
