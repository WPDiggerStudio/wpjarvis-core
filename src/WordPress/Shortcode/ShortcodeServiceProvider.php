<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Shortcode;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers and boots the shortcode system.
 */
class ShortcodeServiceProvider extends ServiceProvider
{
    /**
     * Register the ShortcodeRegistrar in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('shortcode.registrar', function ($app) {
            return new ShortcodeRegistrar($app);
        });

        $this->app->alias('shortcode.registrar', ShortcodeRegistrar::class);
    }

    /**
     * Hook shortcode registration into the WordPress init action.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('init', [$this->app['shortcode.registrar'], 'register']);
    }
}