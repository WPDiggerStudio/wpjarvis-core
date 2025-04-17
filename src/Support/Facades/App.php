<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

use WPJarvis\Core\Foundation\Application;

/**
 * @method static mixed make(string $abstract, array $parameters = [])
 * @method static bool has(string $abstract)
 * @method static void bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false)
 * @method static void singleton(string $abstract, \Closure|string|null $concrete = null)
 * @method static mixed call(callable|string $callback, array $parameters = [])
 * @method static bool isBooted()
 *
 * @see \WPJarvis\Core\Foundation\Application
 */
class App extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'app';
    }
}
