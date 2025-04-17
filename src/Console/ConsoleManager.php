<?php

declare(strict_types=1);

namespace WPJarvis\Core\Console;

use WPJarvis\Core\Container\Container;

/**
 * Class ConsoleManager
 *
 * Handles CLI-specific functionality such as checking for WP-CLI,
 * registering commands, and preparing directories for CLI tools.
 */
class ConsoleManager
{
    /**
     * Application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Create a new console manager instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create a directory if it does not exist.
     *
     * @param string $dir
     * @return bool
     */
    public function makeDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return mkdir($dir, 0755, true);
        }

        return true;
    }

    /**
     * Determine if WP-CLI is available in the environment.
     *
     * @return bool
     */
    public function hasWpCli(): bool
    {
        return class_exists('WP_CLI');
    }

    /**
     * Dynamically add and register a command with WP-CLI.
     *
     * @param class-string<Command> $commandClass
     * @return void
     * @throws \Exception
     */
    public function add(string $commandClass): void
    {
        /** @var CommandServiceProvider|null $provider */
        $provider = $this->app->getProvider(CommandServiceProvider::class);

        if ($provider !== null) {
            $provider->withCommands([$commandClass]);

            if ($this->hasWpCli() && $this->app->isBooted()) {
                /** @var Command $command */
                $command = $this->app->make($commandClass);
                $command->register();
            }
        }
    }

    /**
     * Check if the application is running in CLI mode.
     *
     * @return bool
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli' || defined('WP_CLI');
    }
}
