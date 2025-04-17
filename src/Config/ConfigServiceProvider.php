<?php

declare(strict_types=1);

namespace WPJarvis\Core\Config;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Support\ServiceProvider;
use Illuminate\Config\Repository;

/**
 * Class ConfigServiceProvider
 *
 * Registers the Config class and loads configuration files from a directory.
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * The default path to configuration files.
     *
     * @var string|null
     */
    protected ?string $configPath = null;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('config', function () {
            $repository = new Repository();
            return new Config($repository);
        });
    }

    /**
     * Bootstrap configuration services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        if ($this->configPath) {
            $this->loadConfigFiles();
        }
    }

    /**
     * Set the configuration path (e.g. resources/config).
     *
     * @param string $path
     * @return $this
     */
    public function setConfigPath(string $path): static
    {
        $this->configPath = rtrim($path, '/');
        return $this;
    }

    /**
     * Load all configuration files from the config directory.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function loadConfigFiles(): void
    {
        if (!is_dir($this->configPath)) {
            return;
        }

        /** @var Config $config */
        $config = $this->app->make('config');

        $files = glob($this->configPath . '/*.php') ?: [];

        foreach ($files as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $config->load($file, $key);
        }
    }
}