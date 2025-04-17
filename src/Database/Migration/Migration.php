<?php

declare(strict_types=1);

namespace WPJarvis\Database\Migration;

use wpdb;

/**
 * Base Migration class for database schema changes
 * inspired by Laravel's migration structure.
 */
abstract class Migration
{
    /**
     * The WordPress database object.
     *
     * @var wpdb
     */
    protected wpdb $wpdb;

    /**
     * The database charset collation.
     *
     * @var string
     */
    protected string $charsetCollate;

    /**
     * Database prefix.
     *
     * @var string
     */
    protected string $prefix;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->charsetCollate = $this->wpdb->get_charset_collate();
        $this->prefix = $this->wpdb->prefix;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    abstract public function down(): void;

    /**
     * Create a new table using dbDelta.
     *
     * @param string $tableName
     * @param string $columnsSql
     * @return void
     */
    protected function createTable(string $tableName, string $columnsSql): void
    {
        $table = $this->prefix . $tableName;
        $sql = "CREATE TABLE {$table} (
            {$columnsSql}
        ) {$this->charsetCollate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Drop a table from the database.
     *
     * @param string $tableName
     * @return void
     */
    protected function dropTable(string $tableName): void
    {
        $table = $this->prefix . $tableName;
        $this->wpdb->query("DROP TABLE IF EXISTS {$table}");
    }

    /**
     * Add an index to an existing table.
     *
     * @param string $tableName
     * @param string $indexName
     * @param string|array $columns
     * @return void
     */
    protected function addIndex(string $tableName, string $indexName, string|array $columns): void
    {
        $table = $this->prefix . $tableName;
        $cols = is_array($columns) ? implode(',', $columns) : $columns;

        $this->wpdb->query("ALTER TABLE {$table} ADD INDEX {$indexName} ({$cols})");
    }

    /**
     * Drop an index from an existing table.
     *
     * @param string $tableName
     * @param string $indexName
     * @return void
     */
    protected function dropIndex(string $tableName, string $indexName): void
    {
        $table = $this->prefix . $tableName;
        $this->wpdb->query("ALTER TABLE {$table} DROP INDEX {$indexName}");
    }

    /**
     * Check if a table exists.
     *
     * @param string $tableName
     * @return bool
     */
    protected function tableExists(string $tableName): bool
    {
        $table = $this->prefix . $tableName;
        $name = $this->wpdb->get_var("SHOW TABLES LIKE '{$table}'");
        return $name === $table;
    }

    /**
     * Check if a column exists in a table.
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function columnExists(string $tableName, string $columnName): bool
    {
        $table = $this->prefix . $tableName;
        $column = $this->wpdb->get_results("SHOW COLUMNS FROM {$table} LIKE '{$columnName}'");

        return !empty($column);
    }

    /**
     * Rename a table.
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    protected function renameTable(string $from, string $to): void
    {
        $fromTable = $this->prefix . $from;
        $toTable = $this->prefix . $to;

        $this->wpdb->query("RENAME TABLE {$fromTable} TO {$toTable}");
    }

    /**
     * Run a raw SQL statement.
     *
     * @param string $sql
     * @return void
     */
    protected function statement(string $sql): void
    {
        $this->wpdb->query($sql);
    }

    /**
     * Utility method to get prefixed table name.
     *
     * @param string $table
     * @return string
     */
    protected function table(string $table): string
    {
        return $this->prefix . $table;
    }
}