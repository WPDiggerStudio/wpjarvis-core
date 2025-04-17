<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static \WPJarvis\Core\WordPress\PostType\PostType postType(string $name, string|null $singular = null, string|null $plural = null)
 * @method static array getPostTypes()
 * @method static void register()
 *
 * @see \WPJarvis\Core\WordPress\PostType\PostTypeRegistrar
 */
class PostType extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'posttype.registrar';
    }
}

