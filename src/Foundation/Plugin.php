<?php

declare(strict_types=1);

namespace WPJarvis\Core\Foundation;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Support\ServiceProvider;

/**
 * The WPJarvis Plugin base class.
 *
 * Designed for extensible, modular WordPress plugin development using Laravel-style features.
 */
abstract class Plugin
{
    /**
     * The WPJarvis application container.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * The plugin file path (main file).
     *
     * @var string
     */
    protected string $pluginFile;

    /**
     * Bootstraps the plugin and application.
     *
     * @param string $pluginFile
     * @return void
     */
    public function bootstrap(string $pluginFile): void
    {
        $this->pluginFile = $pluginFile;
        $this->app = new Application(plugin_dir_path($pluginFile), $pluginFile);

        $this->registerHooks();
        $this->registerServiceProviders();
    }

    /**
     * Register WordPress lifecycle and runtime hooks.
     *
     * @return void
     */
    protected function registerHooks(): void
    {
        register_activation_hook($this->pluginFile, [$this, 'activate']);
        register_deactivation_hook($this->pluginFile, [$this, 'deactivate']);
        register_uninstall_hook($this->pluginFile, [static::class, 'uninstall']);

        add_action('plugins_loaded', [$this, 'initialize']);
    }

    /**
     * Register all service providers required by the plugin.
     *
     * @return void
     */
    protected function registerServiceProviders(): void
    {
        foreach ($this->providers() as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * List of service providers to register.
     *
     * @return array<class-string<ServiceProvider>>
     */
    protected function providers(): array
    {
        return [];
    }

    /**
     * Called during WordPress 'plugins_loaded' hook.
     * Bootstraps app lifecycle and translation loading.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->loadTranslations();
        $this->registerFacades();
        $this->bootProviders();

        $this->app->boot();
    }

    /**
     * Load WordPress translation files.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function loadTranslations(): void
    {
        $domain = $this->config('app.text_domain');
        if (!empty($domain)) {
            load_plugin_textdomain(
                $domain,
                false,
                $this->app->pluginDirName() . '/languages'
            );
        }
    }

    /**
     * Register global facades (if needed).
     *
     * @return void
     */
    protected function registerFacades(): void
    {
        // Optional: load custom facades here if necessary
    }

    /**
     * Boot all registered service providers.
     *
     * @return void
     */
    protected function bootProviders(): void
    {
        foreach ($this->app->getProviders() as $provider) {
            $provider->boot();

            if (method_exists($provider, 'registerHooks')) {
                $provider->registerHooks();
            }
        }
    }

    /**
     * Plugin activation hook.
     *
     * @return void
     */
    public function activate(): void
    {
        // Optional: override in plugin
    }

    /**
     * Plugin deactivation hook.
     *
     * @return void
     */
    public function deactivate(): void
    {
        // Optional: override in plugin
    }

    /**
     * Plugin uninstall logic.
     * Must be static as required by WordPress.
     *
     * @return void
     */
    public static function uninstall(): void
    {
        // Optional: override in plugin
    }

    /**
     * Get the WPJarvis application instance.
     *
     * @return Application
     */
    public function app(): Application
    {
        return $this->app;
    }

    /**
     * Get the plugin version from config.
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function version(): string
    {
        return $this->config('app.version', '1.0.0');
    }

    /**
     * Get the plugin name from config.
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function name(): string
    {
        return $this->config('app.name', 'wpjarvis');
    }

    /**
     * Get the text domain from config.
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function textDomain(): string
    {
        return $this->config('app.text_domain', 'wpjarvis');
    }

    /**
     * Read a value from the application config.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws BindingResolutionException
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return $this->app->make('config')->get($key, $default);
    }
}