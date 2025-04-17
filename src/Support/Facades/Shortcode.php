<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static \WPJarvis\Core\WordPress\Shortcode\Shortcode add(string $tag, callable|null $callback = null)
 * @method static array getShortcodes()
 * @method static void register()
 *
 * @see \WPJarvis\Core\WordPress\Shortcode\ShortcodeRegistrar
 */
class Shortcode extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shortcode.registrar';
    }
}

