<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;

/**
 * @method static \wpdb getWpdb()
 * @method static array select(string $sql)
 * @method static int insert(string $table, array $data)
 * @method static int update(string $table, array $data, array $where)
 * @method static int delete(string $table, array $where)
 *
 * @see \WPJarvis\Core\Database\Query
 */
class DB extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'db';
    }
}
