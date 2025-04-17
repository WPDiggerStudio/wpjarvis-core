<?php

declare(strict_types=1);

namespace WPJarvis\Database\Migration;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Service provider for managing and running migrations.
 */
class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Array of migration instances to register.
     *
     * @var array<Migration>
     */
    protected array $migrations = [];

    /**
     * Optional directory for discovering migrations.
     *
     * @var string|null
     */
    protected ?string $discoverPath = null;

    /**
     * Register the migration runner and commands.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('migration', function ($app) {
            return new MigrationRunner($app);
        });

        // Future: Register CLI commands if needed
        if (defined('WP_CLI') && WP_CLI) {
            $this->registerCliCommands();
        }
    }

    /**
     * Bootstrap migration logic and load migrations.
     *
     * @return void
     */
    public function boot(): void
    {
        $runner = $this->app->make('migration');

        // Register manually specified migrations
        foreach ($this->migrations as $migration) {
            $runner->register($migration);
        }

        // Discover and register from a directory
        if ($this->discoverPath) {
            $runner->discover($this->discoverPath);
        }
    }

    /**
     * Register WP-CLI commands (stub for future extension).
     *
     * @return void
     */
    protected function registerCliCommands(): void
    {
        // Example: WP_CLI::add_command('migrate', ...);
    }

    /**
     * Add a migration instance.
     *
     * @param Migration $migration
     * @return $this
     */
    public function add(Migration $migration): static
    {
        $this->migrations[] = $migration;
        return $this;
    }

    /**
     * Set a directory to automatically discover migration classes.
     *
     * @param string $path
     * @return $this
     */
    public function discoverFrom(string $path): static
    {
        $this->discoverPath = $path;
        return $this;
    }
}