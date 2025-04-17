<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static \WPJarvis\Core\Http\Response make(mixed $content = '', int $status = 200, array $headers = [])
 * @method static \WPJarvis\Core\Http\Response json(mixed $data, int $status = 200, array $headers = [])
 * @method static \WPJarvis\Core\Http\Response error(string $message, int $status = 400, array $headers = [])
 * @method static \WPJarvis\Core\Http\Response success(mixed $data, int $status = 200, array $headers = [])
 *
 * @see \WPJarvis\Core\Http\Response
 */
class Response extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \WPJarvis\Core\Http\Response::class;
    }
}
