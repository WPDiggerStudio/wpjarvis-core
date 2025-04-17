<?php

declare(strict_types=1);

namespace WPJarvis\Core\Support\Facades;


/**
 * @method static string generate(string $action = '-1')
 * @method static int|false verify(string $nonce, string $action = '-1')
 * @method static string|void field(string $action = '-1', string $name = '_wpnonce', bool $referer = true, bool $echo = true)
 * @method static string url(string $actionUrl, string $action = '-1', string $name = '_wpnonce')
 * @method static int|false checkAdminReferer(string $action = '-1', string $queryArg = '_wpnonce')
 * @method static int|false checkAjaxReferer(string $action = '-1', string $queryArg = '_wpnonce', bool $die = true)
 *
 * @see \WPJarvis\Security\Nonce
 */
class Nonce extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nonce';
    }
}

