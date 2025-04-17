<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Traits;

use Closure;
use ReflectionClass;
use BadMethodCallException;

/**
 * Trait Macroable
 *
 * Allows dynamically adding methods (macros) to a class.
 * Inspired by Laravel's Macroable trait.
 */
trait Macroable
{
    /**
     * The registered string macros.
     *
     * @var array<string, Closure>
     */
    protected static array $macros = [];

    /**
     * Register a custom macro.
     *
     * @param string $name
     * @param Closure $macro
     * @return void
     */
    public static function macro(string $name, Closure $macro): void
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Mix in another object’s public methods as macros.
     *
     * @param object $mixin
     * @param bool $replace
     * @return void
     */
    public static function mixin(object $mixin, bool $replace = true): void
    {
        $reflection = new ReflectionClass($mixin);

        foreach ($reflection->getMethods() as $method) {
            if (!$method->isPublic() || $method->isStatic()) {
                continue;
            }

            $name = $method->getName();

            if (!$replace && isset(static::$macros[$name])) {
                continue;
            }

            $method->setAccessible(true);
            static::$macros[$name] = $method->getClosure($mixin);
        }
    }

    /**
     * Checks if macro is registered.
     *
     * @param string $name
     * @return bool
     */
    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    /**
     * Handle dynamic calls to macros.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (!static::hasMacro($method)) {
            throw new BadMethodCallException("Method [{$method}] does not exist.");
        }

        $macro = static::$macros[$method];

        if ($macro instanceof Closure) {
            return $macro->bindTo($this, static::class)(...$parameters);
        }

        return $macro(...$parameters);
    }

    /**
     * Handle dynamic static calls to macros.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $method, array $parameters): mixed
    {
        if (!static::hasMacro($method)) {
            throw new BadMethodCallException("Static method [{$method}] does not exist.");
        }

        $macro = static::$macros[$method];

        return $macro(...$parameters);
    }
}
