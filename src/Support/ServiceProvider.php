<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Base service provider for WPJarvis applications.
 *
 * Provides integration with WordPress-specific features like hooks,
 * as well as Laravel-like service registration.
 */
abstract class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register any WordPress hooks for this service provider.
     *
     * Override this method in child classes to register filters and actions.
     *
     * @return void
     */
    public function registerHooks(): void
    {
        // Intentionally left blank. To be optionally overridden by child classes.
    }

    /**
     * Get the WordPress hook prefix for this service provider.
     *
     * Helps to namespace hooks in a consistent manner across packages.
     *
     * @return string
     */
    protected function getHookPrefix(): string
    {
        return 'wpjarvis';
    }

    /**
     * Get a fully qualified prefixed hook name.
     *
     * @param string $hook
     * @return string
     */
    protected function prefixedHook(string $hook): string
    {
        return $this->getHookPrefix() . '.' . $hook;
    }

    /**
     * Register a WordPress action hook.
     *
     * @param string $hook
     * @param callable|string $callback
     * @param int $priority
     * @param int $args
     * @return void
     */
    protected function addAction(string $hook, callable|string $callback, int $priority = 10, int $args = 1): void
    {
        add_action($this->prefixedHook($hook), $this->resolveCallback($callback), $priority, $args);
    }

    /**
     * Register a WordPress filter hook.
     *
     * @param string $hook
     * @param callable|string $callback
     * @param int $priority
     * @param int $args
     * @return void
     */
    protected function addFilter(string $hook, callable|string $callback, int $priority = 10, int $args = 1): void
    {
        add_filter($this->prefixedHook($hook), $this->resolveCallback($callback), $priority, $args);
    }

    /**
     * Convert a string-based callback into a real callable.
     *
     * @param callable|string $callback
     * @return callable
     */
    protected function resolveCallback(callable|string $callback): callable
    {
        if (is_string($callback) && str_contains($callback, '@')) {
            [$class, $method] = explode('@', $callback);
            return function (...$args) use ($class, $method) {
                $instance = $this->app->make($class);
                return $instance->$method(...$args);
            };
        }

        return $callback;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }
}