<?php
declare(strict_types=1);

namespace WPJarvis\Core\Config\Console;

use WPJarvis\Core\Console\Command;
use WPJarvis\Core\Foundation\ConfigCache\ConfigCacheManager;

/**
 * WPJarvis CLI command to cache the configuration.
 *
 * @package WPJarvis\Core\Config\Console
 */
class CacheConfigCommand extends Command
{
    /**
     * The CLI signature.
     *
     * @var string
     */
    protected string $signature = 'config:cache';

    /**
     * The CLI description.
     *
     * @var string
     */
    protected string $description = 'Cache the configuration files into a single file for faster access.';

    /**
     * Handle the command.
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     * @throws \Exception
     */
    public function handle(array $args, array $assoc_args): void
    {
        $cache = new ConfigCacheManager($this->app);
        $config = $this->app->make('config');

        $cache->cache($config->all());

        $this->info('Configuration cached successfully.');
    }
}
