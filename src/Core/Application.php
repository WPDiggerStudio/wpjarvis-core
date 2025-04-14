<?php

declare(strict_types=1);

namespace WPJarvis\Core;

use WPJarvis\Support\Config;

/**
 * Class Application
 *
 * The core WPJarvis plugin bootstrapper.
 * Loads plugin config, lifecycle hooks, and components.
 *
 * @package WPJarvis\Core
 */
class Application
{
    /**
     * Absolute path to the plugin base directory.
     *
     * @var string
     */
    protected string $basePath;

    /**
     * Application constructor.
     *
     * @param string $basePath Path to the root of the plugin
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Boot the plugin by loading config, lifecycle hooks, and components.
     *
     * @return void
     */
    public function boot(): void
    {
        Config::load($this->basePath);
        $this->registerLifecycleHooks();
        $this->autoloadConfiguredFiles();
    }

    /**
     * Register activation and deactivation lifecycle hooks.
     *
     * @return void
     */
    protected function registerLifecycleHooks(): void
    {
        $namespace = Config::get('namespace', 'App');

        $pluginFile = $this->basePath . '/plugin.php';

        $activate   = $namespace . '\\Boot\\Activate';
        $deactivate = $namespace . '\\Boot\\Deactivate';

        if (class_exists($activate)) {
            register_activation_hook($pluginFile, fn() => (new $activate())());
        }

        if (class_exists($deactivate)) {
            register_deactivation_hook($pluginFile, fn() => (new $deactivate())());
        }
    }

    /**
     * Load and run all files listed in the autoload config.
     *
     * @return void
     */
    protected function autoloadConfiguredFiles(): void
    {
        $autoload = Config::get('autoload', []);

        foreach ($autoload as $key => $file) {
            if (!file_exists($file)) {
                continue;
            }

            $result = require $file;

            if (is_callable($result)) {
                $result(); // closure or invokable object
            } elseif (is_object($result) && method_exists($result, '__invoke')) {
                $result();
            }
        }
    }

    /**
     * Get the plugin base path.
     *
     * @return string
     */
    public function basePath(): string
    {
        return $this->basePath;
    }
}
