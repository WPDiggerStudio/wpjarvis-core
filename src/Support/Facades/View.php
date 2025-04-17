<?php
declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static string render(string $view, array $data = [])
 * @method static void addNamespace(string $namespace, string $path)
 *
 * @see \WPJarvis\Core\View\ViewManager
 */
class View extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'view';
    }
}
