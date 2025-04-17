<?php

declare(strict_types=1);

namespace WPJarvis\Foundation\ServiceProviders;

use WPJarvis\Core\Support\ServiceProvider;
use WPJarvis\Core\WordPress\Assets\AssetServiceProvider;
use WPJarvis\Core\WordPress\Block\BlockServiceProvider;
use WPJarvis\Core\WordPress\Dashboard\DashboardServiceProvider;
use WPJarvis\Core\WordPress\Editor\EditorServiceProvider;
use WPJarvis\Core\WordPress\Menu\MenuServiceProvider;
use WPJarvis\Core\WordPress\Metabox\MetaboxServiceProvider;
use WPJarvis\Core\WordPress\PostType\PostTypeServiceProvider;
use WPJarvis\Core\WordPress\Shortcode\ShortcodeServiceProvider;
use WPJarvis\Core\WordPress\Taxonomy\TaxonomyServiceProvider;

/**
 * Central service for managing WordPress integrations.
 * Supports grouped, modular, and conditional loading.
 */
class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Providers related to content structure (e.g., CPT, taxonomies).
     *
     * @var array<class-string<ServiceProvider>>
     */
    protected array $structureProviders = [
        TaxonomyServiceProvider::class,
        PostTypeServiceProvider::class
    ];

    /**
     * Providers for UI management (admin bar, editor, dashboard).
     *
     * @var array<class-string<ServiceProvider>>
     */
    protected array $uiProviders = [
        MenuServiceProvider::class,
        MetaboxServiceProvider::class,
        DashboardServiceProvider::class,
        EditorServiceProvider::class,
    ];

    /**
     * Providers for front-end/user-facing integrations.
     *
     * @var array<class-string<ServiceProvider>>
     */
    protected array $frontendProviders = [
        AssetServiceProvider::class,
        BlockServiceProvider::class,
        ShortcodeServiceProvider::class,
    ];

    /**
     * Register all WordPress-related providers.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerProviders($this->structureProviders);
        $this->registerProviders($this->uiProviders);
        $this->registerProviders($this->frontendProviders);
    }

    /**
     * Register a list of service providers.
     *
     * @param array<class-string<ServiceProvider>> $providers
     * @return void
     */
    protected function registerProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Boot all WordPress-specific integration hooks.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('init', function (): void {
            do_action('wpjarvis_wordpress_init');
        });
    }
}
