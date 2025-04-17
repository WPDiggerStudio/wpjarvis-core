<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static \WPJarvis\Core\WordPress\Assets\Script addScript(string $handle, string $src, array $deps = [], string|bool|null $version = null, bool $inFooter = true)
 * @method static \WPJarvis\Core\WordPress\Assets\Style addStyle(string $handle, string $src, array $deps = [], string|bool|null $version = null, string $media = 'all')
 * @method static void register()
 * @method static void enqueue()
 * @method static array getScripts()
 * @method static array getStyles()
 *
 * @see \WPJarvis\Core\WordPress\Assets\AssetRegistrar
 */
class Assets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'assets.registrar';
    }
}

