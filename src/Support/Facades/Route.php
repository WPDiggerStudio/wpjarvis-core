<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * Class Route
 *
 * A facade for the Router service, providing a clean static interface for route definition.
 *
 * @method static \WPJarvis\Core\Routing\Route get(string $uri, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route post(string $uri, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route put(string $uri, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route patch(string $uri, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route delete(string $uri, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route api(string $method, string $endpoint, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route admin(string $slug, string $title, string|array|\Closure $action, array $options = [])
 * @method static \WPJarvis\Core\Routing\Route ajax(string $action, string|array|\Closure $handler, bool $nopriv = false)
 * @method static array getRoutes()
 * @method static void registerRoutes()
 *
 * @see \WPJarvis\Core\Routing\Router
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
