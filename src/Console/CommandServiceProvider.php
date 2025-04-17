<?php

declare(strict_types=1);

namespace WPJarvis\Core\Console;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Support\ServiceProvider;
use WPJarvis\Core\Console\Commands\GenerateController;
use WPJarvis\Core\Console\Commands\GenerateModel;
use WPJarvis\Core\Queue\Console\WorkCommand;

/**
 * Class CommandServiceProvider
 *
 * Registers all CLI commands with the WPJarvis application
 * and integrates with WP-CLI or Symfony Console if available.
 *
 * @package WPJarvis\Core\Console
 */
class CommandServiceProvider extends ServiceProvider
{
    /**
     * List of console command classes to register.
     *
     * @var array<int, class-string<Command>>
     */
    protected array $commands = [
        GenerateController::class,
        GenerateModel::class,
        WorkCommand::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register ConsoleManager as a singleton
        $this->app->singleton('console', function ($app) {
            return new ConsoleManager($app);
        });

        // Bind command classes to the container
        $this->registerCommands();
    }

    /**
     * Bootstrap services after all providers have registered.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        // Register commands with WP-CLI
        if (class_exists('WP_CLI')) {
            foreach ($this->commands as $command) {
                /** @var Command $instance */
                $instance = $this->app->make($command);
                $instance->register();
            }
        }

        // Optionally support Symfony Console usage
        if ($this->app->bound('console')) {
            $console = $this->app->make('console');

            foreach ($this->commands as $command) {
                $console->add($this->app->make($command));
            }
        }
    }

    /**
     * Bind command classes into the container as singletons.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        foreach ($this->commands as $command) {
            $this->app->singleton($command, fn($app) => new $command($app));
        }
    }

    /**
     * Add more commands dynamically to the provider.
     *
     * @param array<int, class-string<Command>> $commands
     * @return static
     */
    public function withCommands(array $commands): static
    {
        $this->commands = array_merge($this->commands, $commands);

        return $this;
    }
}