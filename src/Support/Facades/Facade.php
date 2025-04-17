<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

use WPJarvis\Core\Foundation\Application;
use RuntimeException;

/**
 * Class Facade
 *
 * Provides a static "facade" interface to classes bound in the container.
 * Inspired by Laravel's facade system, adapted for WPJarvis.
 *
 * @package WPJarvis\Core\Support\Facades
 */
abstract class Facade
{
    /**
     * The application container instance.
     *
     * @var Application
     */
    protected static Application $app;

    /**
     * The resolved object instances.
     *
     * @var array<string, mixed>
     */
    protected static array $resolvedInstance = [];

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public static function getFacadeRoot(): mixed
    {
        $accessor = static::getFacadeAccessor();

        if (!is_string($accessor)) {
            throw new RuntimeException('Facade accessor must return a string.');
        }

        return static::resolveFacadeInstance($accessor);
    }

    /**
     * Get the registered name of the component in the container.
     *
     * Child classes must override this method to return the service key.
     *
     * @return string
     *
     * @throws RuntimeException If not implemented in subclass.
     */
    protected static function getFacadeAccessor(): string
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param string $name
     * @return mixed
     */
    protected static function resolveFacadeInstance(string $name): mixed
    {
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        if (!isset(static::$app)) {
            throw new RuntimeException('Application instance has not been set on the Facade.');
        }

        return static::$resolvedInstance[$name] = static::$app[$name];
    }

    /**
     * Set the application instance.
     *
     * @param Application $app
     * @return void
     */
    public static function setFacadeApplication(Application $app): void
    {
        static::$app = $app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws RuntimeException If no instance is resolved.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->{$method}(...$args);
    }
}