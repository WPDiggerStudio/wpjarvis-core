<?php

declare(strict_types=1);

namespace WPJarvis\Database\Migration;

use WPJarvis\Foundation\Application;
use wpdb;
use RuntimeException;

/**
 * Migration runner class for executing and rolling back migrations.
 */
class MigrationRunner
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * The WordPress database object.
     *
     * @var wpdb
     */
    protected wpdb $wpdb;

    /**
     * The migrations table name.
     *
     * @var string
     */
    protected string $table;

    /**
     * Registered migration instances.
     *
     * @var array<Migration>
     */
    protected array $migrations = [];

    /**
     * Create a new migration runner instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        global $wpdb;

        $this->app = $app;
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'wpjarvis_migrations';

        $this->ensureMigrationTableExists();
    }

    /**
     * Ensure the migrations table exists.
     *
     * @return void
     */
    protected function ensureMigrationTableExists(): void
    {
        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$this->table}'") !== $this->table) {
            $charsetCollate = $this->wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$this->table} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                migration varchar(255) NOT NULL,
                batch int(11) NOT NULL,
                PRIMARY KEY (id)
            ) {$charsetCollate};";

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
    }

    /**
     * Register a migration class instance.
     *
     * @param Migration $migration
     * @return void
     */
    public function register(Migration $migration): void
    {
        $this->migrations[] = $migration;
    }

    /**
     * Run all pending migrations.
     *
     * @return void
     */
    public function run(): void
    {
        $batch = $this->getLastBatchNumber() + 1;
        $ran = $this->getRanMigrations();

        foreach ($this->migrations as $migration) {
            $className = get_class($migration);

            if (!in_array($className, $ran, true)) {
                try {
                    $migration->up();
                    $this->addMigrationRecord($className, $batch);
                } catch (\Throwable $e) {
                    throw new RuntimeException("Migration failed: {$className} - " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Roll back a specific batch or the last batch.
     *
     * @param int|null $batch
     * @return void
     */
    public function rollback(?int $batch = null): void
    {
        $batch = $batch ?? $this->getLastBatchNumber();
        $migrations = $this->getMigrationsFromBatch($batch);

        foreach (array_reverse($migrations) as $migrationClass) {
            $migration = $this->findMigrationInstance($migrationClass);

            if ($migration) {
                $migration->down();
                $this->removeMigrationRecord($migrationClass);
            }
        }
    }

    /**
     * Refresh all migrations: rollback then re-run.
     *
     * @return void
     */
    public function refresh(): void
    {
        while ($this->getLastBatchNumber() > 0) {
            $this->rollback();
        }

        $this->run();
    }

    /**
     * Find a migration instance by class name.
     *
     * @param string $className
     * @return Migration|null
     */
    protected function findMigrationInstance(string $className): ?Migration
    {
        foreach ($this->migrations as $migration) {
            if (get_class($migration) === $className) {
                return $migration;
            }
        }

        return null;
    }

    /**
     * Get the last applied migration batch number.
     *
     * @return int
     */
    protected function getLastBatchNumber(): int
    {
        return (int)($this->wpdb->get_var("SELECT MAX(batch) FROM {$this->table}") ?? 0);
    }

    /**
     * Get all previously run migration class names.
     *
     * @return array
     */
    protected function getRanMigrations(): array
    {
        return $this->wpdb->get_col("SELECT migration FROM {$this->table}");
    }

    /**
     * Get migrations from a specific batch.
     *
     * @param int $batch
     * @return array
     */
    protected function getMigrationsFromBatch(int $batch): array
    {
        return $this->wpdb->get_col(
            $this->wpdb->prepare("SELECT migration FROM {$this->table} WHERE batch = %d", $batch)
        );
    }

    /**
     * Log a migration to the database.
     *
     * @param string $migration
     * @param int $batch
     * @return void
     */
    protected function addMigrationRecord(string $migration, int $batch): void
    {
        $this->wpdb->insert($this->table, [
            'migration' => $migration,
            'batch' => $batch,
        ]);
    }

    /**
     * Remove a migration record after rollback.
     *
     * @param string $migration
     * @return void
     */
    protected function removeMigrationRecord(string $migration): void
    {
        $this->wpdb->delete($this->table, ['migration' => $migration]);
    }

    /**
     * Automatically discover and register migration classes from a directory.
     *
     * @param string $directory Absolute path to migration files.
     * @return void
     * @throws \ReflectionException
     */
    public function discover(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (glob(rtrim($directory, '/') . '/*.php') as $file) {
            require_once $file;

            // Try to get all classes loaded after this file was included
            foreach (get_declared_classes() as $class) {
                if (is_subclass_of($class, Migration::class) && !(new \ReflectionClass($class))->isAbstract()) {
                    $this->register(new $class());
                }
            }
        }
    }

}
