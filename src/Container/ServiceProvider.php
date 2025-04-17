<?php

declare(strict_types=1);

namespace WPJarvis\Core\Container;

/**
 * Service Provider Base Class
 *
 * Service providers are the central place to configure your application.
 * This class provides a base implementation for service providers.
 */
abstract class ServiceProvider
{
    /**
     * The application instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = false;

    /**
     * The paths that should be published.
     *
     * @var array<string, array>
     */
    protected array $publishes = [];

    /**
     * Create a new service provider instance.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    abstract public function register(): void;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Default implementation does nothing
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract
     * @param string|\Closure|null $concrete
     * @param bool $shared
     * @return void
     */
    protected function bind(string $abstract, string|\Closure|null $concrete = null, bool $shared = false): void
    {
        $this->app->bind($abstract, $concrete, $shared);
    }

    /**
     * Register a shared binding in the container.
     *
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @return void
     */
    protected function singleton(string $abstract, string|\Closure|null $concrete = null): void
    {
        $this->app->singleton($abstract, $concrete);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when(): array
    {
        return [];
    }

    /**
     * Determine if the provider is deferred.
     *
     * @return bool
     */
    public function isDeferred(): bool
    {
        return $this->defer;
    }

    /**
     * Register paths to be published by the publish command.
     *
     * @param array $paths
     * @param string|null $group
     * @return void
     */
    protected function publishes(array $paths, ?string $group = null): void
    {
        $class = get_class($this);

        if (!isset($this->publishes[$class])) {
            $this->publishes[$class] = [];
        }

        $this->publishes[$class] = array_merge($this->publishes[$class], $paths);

        if ($group) {
            if (!isset($this->publishGroups[$group])) {
                $this->publishGroups[$group] = [];
            }

            $this->publishGroups[$group] = array_merge(
                $this->publishGroups[$group], $paths
            );
        }
    }

    /**
     * Load the given view file path.
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    protected function loadViewsFrom(string $path, string $namespace): void
    {
        if ($this->app->has('view')) {
            $this->app->make('view')->addNamespace($namespace, $path);
        }
    }

    /**
     * Register a view file namespace.
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    /**
     * Register a translations namespace from a given path.
     *
     * @param string $path
     * @param string $namespace
     * @return void
     */
    protected function loadTranslationsFrom(string $path, string $namespace): void
    {
        // Ensure path exists
        if (!is_dir($path)) {
            return;
        }

        // Build textdomain key from namespace
        $domain = strtolower(str_replace(['\\', '/'], '-', $namespace));

        // Register the textdomain with WordPress
        add_action('init', function () use ($domain, $path) {
            load_plugin_textdomain($domain, false, $path);
        });
    }


    /**
     * Register database migration paths.
     *
     * @param string $path
     * @return void
     * @throws \Exception
     */
    protected function loadMigrationsFrom(string $path): void
    {
        if ($this->app->has('migration.repository')) {
            $this->app->make('migration.repository')->registerPath($path);
        }
    }

    /**
     * Register routes.
     *
     * @param string $path
     * @return void
     */
    protected function loadRoutesFrom(string $path): void
    {
        if ($this->app->has('router') && file_exists($path)) {
            require $path;
        }
    }

    /**
     * Add a WordPress action hook.
     *
     * @param string $hook
     * @param callable|string $callback
     * @param int $priority
     * @param int $args
     * @return bool
     */
    protected function addAction(string $hook, $callback, int $priority = 10, int $args = 1): bool
    {
        return add_action($hook, $this->createCallableCallback($callback), $priority, $args);
    }

    /**
     * Add a WordPress filter hook.
     *
     * @param string $hook
     * @param callable|string $callback
     * @param int $priority
     * @param int $args
     * @return bool
     */
    protected function addFilter(string $hook, $callback, int $priority = 10, int $args = 1): bool
    {
        return add_filter($hook, $this->createCallableCallback($callback), $priority, $args);
    }

    /**
     * Create a callable callback from a string or callable.
     *
     * @param callable|string $callback
     * @return callable
     */
    protected function createCallableCallback($callback): callable
    {
        // If the callback is a string with Class@method format, resolve it
        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($class, $method) = explode('@', $callback, 2);

            return function (...$args) use ($class, $method) {
                $instance = $this->app->make($class);
                return $instance->{$method}(...$args);
            };
        }

        // If it's a Closure or already callable, return it
        return $callback;
    }
}