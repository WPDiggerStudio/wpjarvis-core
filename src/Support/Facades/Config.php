<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static bool has(string $key)
 * @method static array all()
 * @method static void forget(string $key)
 * @method static void load(string $path, string $key = null)
 *
 * @see \WPJarvis\Core\Config\Config
 */
class Config extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'config';
    }
}
