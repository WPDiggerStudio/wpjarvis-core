<?php

declare(strict_types=1);

namespace WPJarvis\Core\Foundation\Bootstrap;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Config\Config;
use WPJarvis\Core\Foundation\Application;
use WPJarvis\Core\Config\ConfigServiceProvider;
use WPJarvis\Core\Foundation\ConfigCache\ConfigCacheManager;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Bootstrap the application's configuration files.
 */
class LoadConfiguration
{
    /**
     * Bootstrap configuration loading.
     *
     * @param Application $app
     * @return void
     * @throws BindingResolutionException
     */
    public function bootstrap(Application $app): void
    {
        // Register config system
        $app->register(new ConfigServiceProvider($app));

        /** @var Config $config */
        $config = $app->make('config');

        $filesystem = new Filesystem();
        $finder = new Finder();

        $configPath = $app->basePath('config');
        $defaultsPath = $configPath . '/defaults';
        $env = $app->environment();
        $envPath = $configPath . '/' . $env;

        // Caching logic
        $cache = new ConfigCacheManager($app);

        if ($cache->isCached()) {
            $cached = $cache->getCached();
            foreach ($cached as $key => $value) {
                $config->set($key, $value);
            }
            return;
        }

        $loaded = [];

        // Load defaults
        if (is_dir($defaultsPath)) {
            foreach ($finder->files()->in($defaultsPath)->name('*.php') as $file) {
                $key = $file->getBasename('.php');
                $loaded[$key] = require $file->getRealPath();
            }
        }

        // Load main config (overrides defaults)
        if (is_dir($configPath)) {
            foreach ($finder->files()->in($configPath)->depth('== 0')->name('*.php') as $file) {
                $key = $file->getBasename('.php');
                $loaded[$key] = array_merge(
                    $loaded[$key] ?? [],
                    require $file->getRealPath()
                );
            }
        }

        // Load env-specific overrides
        if (is_dir($envPath)) {
            foreach ($finder->files()->in($envPath)->name('*.php') as $file) {
                $key = $file->getBasename('.php');
                $loaded[$key] = array_merge(
                    $loaded[$key] ?? [],
                    require $file->getRealPath()
                );
            }
        }

        // Apply loaded config to container
        foreach ($loaded as $key => $value) {
            $config->set($key, $value);
        }

        // Cache for next load
        $cache->cache($loaded);
    }
}