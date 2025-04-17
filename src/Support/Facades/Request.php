<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static array all()
 * @method static mixed input(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static string method()
 * @method static string uri()
 * @method static string ip()
 *
 * @see \WPJarvis\Core\Http\Request
 */
class Request extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'request';
    }
}
