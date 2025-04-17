<?php

use WPJarvis\Core\Container\Container;
use WPJarvis\Core\Http\Request;
use WPJarvis\Core\Http\Response;
use WPJarvis\Core\Validation\Validator;

// ---------------------------------------------
// Core: app() / container
// ---------------------------------------------
if (!function_exists('upap_app')) {
    function upap_app(string $abstract = null, array $parameters = []): mixed
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

// ---------------------------------------------
// Configuration
// ---------------------------------------------
if (!function_exists('upap_config')) {
    function upap_config(string|array|null $key = null, mixed $default = null): mixed
    {
        $config = app('config');

        if (is_null($key)) return $config;

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $config->set($k, $v);
            }
            return true;
        }

        return $config->get($key, $default);
    }
}

// ---------------------------------------------
// Views / Blade
// ---------------------------------------------
if (!function_exists('upap_view')) {
    function upap_view(string $view = null, array $data = [], array $mergeData = []): mixed
    {
        $factory = app('view');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}

// ---------------------------------------------
// Path Helpers
// ---------------------------------------------
if (!function_exists('upap_base_path')) {
    function upap_base_path(string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (!function_exists('upap_plugin_path')) {
    function upap_plugin_path(string $path = ''): string
    {
        return app()->pluginPath($path);
    }
}

if (!function_exists('upap_resource_path')) {
    function upap_resource_path(string $path = ''): string
    {
        return plugin_path('resources' . ($path ? "/{$path}" : ''));
    }
}

if (!function_exists('upap_storage_path')) {
    function upap_storage_path(string $path = ''): string
    {
        return plugin_path('storage' . ($path ? "/{$path}" : ''));
    }
}

// ---------------------------------------------
// Routing & Redirect
// ---------------------------------------------
if (!function_exists('upap_route')) {
    function upap_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        return app('router')->route($name, $parameters, $absolute);
    }
}

if (!function_exists('upap_redirect')) {
    function upap_redirect(string $to = '', int $status = 302, array $headers = [], bool $secure = null): mixed
    {
        return app('response')->redirect($to, $status)->setHeaders($headers);
    }
}

// ---------------------------------------------
// Request / Response
// ---------------------------------------------
if (!function_exists('upap_request')) {
    function upap_request(array|string|null $key = null, mixed $default = null): mixed
    {
        $req = app(Request::class);

        if (is_null($key)) {
            return $req;
        }

        if (is_array($key)) {
            return $req->only($key);
        }

        return $req->input($key, $default);
    }
}

if (!function_exists('upap_response')) {
    function upap_response(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

// ---------------------------------------------
// Validator
// ---------------------------------------------
if (!function_exists('upap_validator')) {
    function upap_validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []): Validator|\Illuminate\Contracts\Validation\Validator
    {
        $factory = app('validator');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data, $rules, $messages, $customAttributes);
    }
}

// ---------------------------------------------
// Events / Helpers
// ---------------------------------------------
if (!function_exists('upap_event')) {
    function upap_event(string|object $event, mixed $payload = [], bool $halt = false): mixed
    {
        return app('events')->dispatch($event, $payload, $halt);
    }
}

if (!function_exists('upap_value')) {
    function upap_value(mixed $value): mixed
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

// ---------------------------------------------
// WordPress Hook Helpers
// ---------------------------------------------
if (!function_exists('upap_wpj_action')) {
    function upap_wpj_action(string $hook, callable|string $callback, int $priority = 10, int $args = 1): void
    {
        add_action($hook, $callback, $priority, $args);
    }
}

if (!function_exists('upap_wpj_filter')) {
    function upap_wpj_filter(string $hook, callable|string $callback, int $priority = 10, int $args = 1): void
    {
        add_filter($hook, $callback, $priority, $args);
    }
}

// ---------------------------------------------
// Database
// ---------------------------------------------
if (!function_exists('upap_wpj_db')) {
    function upap_wpj_db(string $connection = null): \WPJarvis\Core\Database\Connection
    {
        return app('db')->connection($connection);
    }
}

// ---------------------------------------------
// Additional Laravel-Style Goodies
// ---------------------------------------------
if (!function_exists('upap_now')) {
    function upap_now(): \Illuminate\Support\Carbon
    {
        return \Illuminate\Support\Carbon::now();
    }
}

if (!function_exists('upap_logger')) {
    function upap_logger(string $message = null, array $context = []): mixed
    {
        $log = app('log');

        if (is_null($message)) {
            return $log;
        }

        return $log->debug($message, $context);
    }
}

if (!function_exists('upap_abort')) {
    function upap_abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        exit($message);
    }
}

if (!function_exists('upap_csrf_token')) {
    function upap_csrf_token(): ?string
    {
        return $_SESSION['_token'] ?? null;
    }
}

if (!function_exists('upap_session')) {
    function upap_session(string $key = null, mixed $default = null): mixed
    {
        return is_null($key)
            ? $_SESSION
            : ($_SESSION[$key] ?? value($default));
    }
}

if (!function_exists('upap_post_type_exists')) {
    /**
     * Check if a post type exists.
     *
     * @param string $postType
     * @return bool
     */
    function upap_post_type_exists(string $postType): bool
    {
        return post_type_exists($postType);
    }
}

if (!function_exists('upap_wp_nonce')) {
    /**
     * Generate a nonce for a given action.
     *
     * @param string $action
     * @return string
     */
    function upap_wp_nonce(string $action = '-1'): string
    {
        return wp_create_nonce($action);
    }
}

if (!function_exists('upap_wp_nonce_field')) {
    /**
     * Output a WordPress nonce field for forms.
     *
     * @param string $action
     * @param string $name
     * @param bool $referer
     * @param bool $echo
     * @return string|null
     */
    function upap_wp_nonce_field(string $action = '-1', string $name = '_wpnonce', bool $referer = true, bool $echo = true): string|null
    {
        return wp_nonce_field($action, $name, $referer, $echo);
    }
}

if (!function_exists('upap_current_user_can')) {
    /**
     * Check if the current user has a capability.
     *
     * @param string $capability
     * @param mixed ...$args
     * @return bool
     */
    function upap_current_user_can(string $capability, ...$args): bool
    {
        return current_user_can($capability, ...$args);
    }
}

if (!function_exists('upap_is_admin')) {
    /**
     * Check if the current request is in the WordPress admin area.
     *
     * @return bool
     */
    function upap_is_admin(): bool
    {
        return is_admin();
    }
}

if (!function_exists('upap_asset_url')) {
    /**
     * Get the full URL to a plugin asset.
     *
     * @param string $path
     * @return string
     */
    function upap_asset_url(string $path = ''): string
    {
        return plugins_url($path, upap_plugin_path());
    }
}

if (!function_exists('upap_get_post_types')) {
    /**
     * Get all registered post types.
     *
     * @param array $args
     * @param string $output
     * @return array
     */
    function upap_get_post_types(array $args = [], string $output = 'names'): array
    {
        return get_post_types($args, $output);
    }
}
