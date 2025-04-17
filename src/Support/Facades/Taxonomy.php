<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static \WPJarvis\Core\WordPress\Taxonomy\Taxonomy taxonomy(string $name, string|null $singular = null, string|null $plural = null, array $postTypes = [])
 * @method static array getTaxonomies()
 * @method static void register()
 *
 * @see \WPJarvis\Core\WordPress\Taxonomy\TaxonomyRegistrar
 */
class Taxonomy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'taxonomy.registrar';
    }
}

