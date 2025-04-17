<?php

declare(strict_types=1);

namespace WPJarvis\Database\Migration;

/**
 * Schema builder for defining table structure in WordPress.
 */
class SchemaBuilder
{
    /**
     * Table name (without prefix).
     *
     * @var string
     */
    protected string $table;

    /**
     * WordPress database prefix.
     *
     * @var string
     */
    protected string $prefix;

    /**
     * Columns definitions.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Primary key.
     *
     * @var string|null
     */
    protected ?string $primaryKey = null;

    /**
     * Unique keys definitions.
     *
     * @var array
     */
    protected array $uniqueKeys = [];

    /**
     * Index keys definitions.
     *
     * @var array
     */
    protected array $indexes = [];

    /**
     * Create a new schema builder.
     *
     * @param string $table
     * @param string $prefix
     */
    public function __construct(string $table, string $prefix)
    {
        $this->table = $table;
        $this->prefix = $prefix;
    }

    /**
     * Get the table name (without prefix).
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the fully qualified table name (with prefix).
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->prefix . $this->table;
    }

    /**
     * Get columns definitions.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the defined primary key.
     *
     * @return string|null
     */
    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    /**
     * Get unique keys.
     *
     * @return array
     */
    public function getUniqueKeys(): array
    {
        return $this->uniqueKeys;
    }

    /**
     * Get indexes.
     *
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * Add an auto-incrementing ID column and set it as primary key.
     *
     * @param string $column
     * @return $this
     */
    public function id(string $column = 'id'): static
    {
        $this->columns[] = "`{$column}` bigint(20) unsigned NOT NULL AUTO_INCREMENT";
        $this->primaryKey = $column;
        return $this;
    }

    /**
     * Add an integer column.
     *
     * @param string $column
     * @param int $length
     * @param bool $unsigned
     * @param bool $nullable
     * @return $this
     */
    public function integer(string $column, int $length = 11, bool $unsigned = false, bool $nullable = false): static
    {
        $type = "int({$length})" . ($unsigned ? " unsigned" : "");
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` {$type} {$null}";
        return $this;
    }

    /**
     * Add a bigint column.
     *
     * @param string $column
     * @param bool $unsigned
     * @param bool $nullable
     * @return $this
     */
    public function bigint(string $column, bool $unsigned = false, bool $nullable = false): static
    {
        $type = "bigint(20)" . ($unsigned ? " unsigned" : "");
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` {$type} {$null}";
        return $this;
    }

    /**
     * Add a string (VARCHAR) column.
     *
     * @param string $column
     * @param int $length
     * @param bool $nullable
     * @return $this
     */
    public function string(string $column, int $length = 255, bool $nullable = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` varchar({$length}) {$null}";
        return $this;
    }

    /**
     * Add a text column.
     *
     * @param string $column
     * @param bool $nullable
     * @return $this
     */
    public function text(string $column, bool $nullable = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` text {$null}";
        return $this;
    }

    /**
     * Add a longtext column.
     *
     * @param string $column
     * @param bool $nullable
     * @return $this
     */
    public function longText(string $column, bool $nullable = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` longtext {$null}";
        return $this;
    }

    /**
     * Add a boolean column.
     *
     * @param string $column
     * @param bool $nullable
     * @return $this
     */
    public function boolean(string $column, bool $nullable = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` tinyint(1) {$null} DEFAULT 0";
        return $this;
    }

    /**
     * Add a datetime column.
     *
     * @param string $column
     * @param bool $nullable
     * @return $this
     */
    public function datetime(string $column, bool $nullable = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $this->columns[] = "`{$column}` datetime {$null}";
        return $this;
    }

    /**
     * Add a timestamp column.
     *
     * @param string $column
     * @param bool $nullable
     * @param bool $useCurrent
     * @return $this
     */
    public function timestamp(string $column, bool $nullable = false, bool $useCurrent = false): static
    {
        $null = $nullable ? "NULL" : "NOT NULL";
        $default = $useCurrent ? " DEFAULT CURRENT_TIMESTAMP" : "";
        $this->columns[] = "`{$column}` timestamp {$null}{$default}";
        return $this;
    }

    /**
     * Add standard created_at and updated_at timestamps.
     *
     * @return $this
     */
    public function timestamps(): static
    {
        return $this
            ->timestamp('created_at', false, true)
            ->timestamp('updated_at', true);
    }

    /**
     * Define a primary key on the table.
     *
     * @param string|array $columns
     * @return $this
     */
    public function primary(string|array $columns): static
    {
        $this->primaryKey = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }

    /**
     * Define a unique key constraint.
     *
     * @param string $name
     * @param string|array $columns
     * @return $this
     */
    public function unique(string $name, string|array $columns): static
    {
        $this->uniqueKeys[$name] = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }

    /**
     * Define an index key.
     *
     * @param string $name
     * @param string|array $columns
     * @return $this
     */
    public function index(string $name, string|array $columns): static
    {
        $this->indexes[$name] = is_array($columns) ? implode(',', $columns) : $columns;
        return $this;
    }
}