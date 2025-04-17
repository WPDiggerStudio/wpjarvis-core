<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Traits;

/**
 * Trait WordPressHooks
 *
 * Provides helper methods to simplify WordPress action and filter registration.
 *
 * @package WPJarvis\Core\Support\Traits
 */
trait WordPressHooks
{
    /**
     * Register a WordPress action hook.
     *
     * @param string $hook The name of the action hook.
     * @param callable $callback The callback to run when the action is triggered.
     * @param int $priority Optional. Order in which the functions associated with this action are executed.
     * @param int $acceptedArgs Optional. Number of arguments the function accepts.
     * @return void
     */
    public function addAction(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_action($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Register multiple action hooks.
     *
     * @param array $actions Format: ['hook' => callback]
     * @param int $priority
     * @param int $acceptedArgs
     * @return void
     */
    public function addActions(array $actions, int $priority = 10, int $acceptedArgs = 1): void
    {
        foreach ($actions as $hook => $callback) {
            $this->addAction($hook, $callback, $priority, $acceptedArgs);
        }
    }

    /**
     * Register a WordPress filter hook.
     *
     * @param string $hook The name of the filter hook.
     * @param callable $callback The callback to filter the value.
     * @param int $priority Optional. Execution order.
     * @param int $acceptedArgs Optional. Number of args the function accepts.
     * @return void
     */
    public function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        add_filter($hook, $callback, $priority, $acceptedArgs);
    }

    /**
     * Register multiple filter hooks.
     *
     * @param array $filters Format: ['hook' => callback]
     * @param int $priority
     * @param int $acceptedArgs
     * @return void
     */
    public function addFilters(array $filters, int $priority = 10, int $acceptedArgs = 1): void
    {
        foreach ($filters as $hook => $callback) {
            $this->addFilter($hook, $callback, $priority, $acceptedArgs);
        }
    }

    /**
     * Remove a WordPress action hook.
     *
     * @param string $hook The name of the action hook.
     * @param callable $callback The callback to remove.
     * @param int $priority Optional. Must match the priority at which it was added.
     * @return void
     */
    public function removeAction(string $hook, callable $callback, int $priority = 10): void
    {
        remove_action($hook, $callback, $priority);
    }

    /**
     * Remove a WordPress filter hook.
     *
     * @param string $hook The name of the filter hook.
     * @param callable $callback The callback to remove.
     * @param int $priority Optional. Must match the priority at which it was added.
     * @return void
     */
    public function removeFilter(string $hook, callable $callback, int $priority = 10): void
    {
        remove_filter($hook, $callback, $priority);
    }

    /**
     * Execute a WordPress action.
     *
     * @param string $hook The action hook name.
     * @param mixed ...$args Arguments passed to the action.
     * @return void
     */
    public function doAction(string $hook, ...$args): void
    {
        do_action($hook, ...$args);
    }

    /**
     * Apply WordPress filters to a value.
     *
     * @param string $hook The filter hook name.
     * @param mixed $value The value to be filtered.
     * @param mixed ...$args Additional arguments passed to the filter callback.
     * @return mixed
     */
    public function applyFilters(string $hook, mixed $value, ...$args): mixed
    {
        return apply_filters($hook, $value, ...$args);
    }
}