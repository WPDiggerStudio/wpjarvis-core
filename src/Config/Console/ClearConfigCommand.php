<?php

declare(strict_types=1);

namespace WPJarvis\Core\Config\Console;

use WPJarvis\Core\Console\Command;
use WPJarvis\Core\Foundation\ConfigCache\ConfigCacheManager;

/**
 * WPJarvis CLI command to clear the config cache.
 *
 * @package WPJarvis\Core\Config\Console
 */
class ClearConfigCommand extends Command
{
    /**
     * The CLI signature.
     *
     * @var string
     */
    protected string $signature = 'config:clear';

    /**
     * The CLI description.
     *
     * @var string
     */
    protected string $description = 'Remove the cached configuration file.';

    /**
     * Handle the command.
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     */
    public function handle(array $args, array $assoc_args): void
    {
        $cache = new ConfigCacheManager($this->app);

        $cache->clear();
        $this->info('Configuration cache cleared.');
    }
}

