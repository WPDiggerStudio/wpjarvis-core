<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Editor;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Service provider for managing the classic WordPress editor (TinyMCE).
 * Binds the editor toolbar into the container and provides extension points.
 */
class EditorServiceProvider extends ServiceProvider
{
    /**
     * Instance of the editor toolbar.
     *
     * @var Toolbar|null
     */
    protected ?Toolbar $toolbar = null;

    /**
     * Register the editor toolbar as a singleton in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('editor.toolbar', function () {
            return new Toolbar();
        });

        $this->app->alias('editor.toolbar', Toolbar::class);
    }

    /**
     * Bootstrap the editor integration and configure the toolbar.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->toolbar = $this->app['editor.toolbar'];

        // Allow subclasses or external packages to customize the toolbar
        $this->configureToolbar($this->toolbar);

        // Register the toolbar modifications with WordPress
        $this->toolbar->register();
    }

    /**
     * Configure the editor toolbar.
     * This method can be overridden by subclasses or extended via custom service providers.
     *
     * @param Toolbar $toolbar The toolbar instance to configure.
     * @return void
     */
    protected function configureToolbar(Toolbar $toolbar): void
    {
        // Intended to be overridden by subclasses.
    }
}