<?php

declare(strict_types=1);

namespace WPJarvis\Database;

use wpdb;

/**
 * Query builder for WordPress database interactions.
 * Inspired by Laravel's fluent query syntax, adapted for $wpdb.
 */
class Query
{
    /**
     * The WordPress database object.
     *
     * @var wpdb
     */
    protected wpdb $wpdb;

    /**
     * The table this query is targeting.
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * The columns to select.
     *
     * @var array
     */
    protected array $select = ['*'];

    /**
     * The where constraints.
     *
     * @var array
     */
    protected array $where = [];

    /**
     * The order by clauses.
     *
     * @var array
     */
    protected array $orderBy = [];

    /**
     * The limit value.
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * The offset value.
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * Create a new query instance.
     *
     * @param string|null $table
     */
    public function __construct(?string $table = null)
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table = $table;
    }

    /**
     * Set the table for the query.
     *
     * @param string $table
     * @return $this
     */
    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array|string $columns
     * @return $this
     */
    public function select(array|string $columns = ['*']): static
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add a where clause to the query.
     *
     * @param string $column
     * @param string|int|float|null $operator
     * @param mixed|null $value
     * @return $this
     */
    public function where(string $column, string|int|float|null $operator = null, mixed $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = compact('column', 'operator', 'value');

        return $this;
    }

    /**
     * Add an order by clause to the query.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => strtoupper($direction),
        ];

        return $this;
    }

    /**
     * Set the limit for the query.
     *
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the offset for the query.
     *
     * @param int $offset
     * @return $this
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute a SELECT query.
     *
     * @return array
     */
    public function get(): array
    {
        $query = $this->buildSelectQuery();
        return $this->wpdb->get_results($query, ARRAY_A) ?? [];
    }

    /**
     * Get the first result from the query.
     *
     * @return array|null
     */
    public function first(): ?array
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    /**
     * Get a single column's values from the result set.
     *
     * @param string $column
     * @return array
     */
    public function pluck(string $column): array
    {
        return array_column($this->get(), $column);
    }

    /**
     * Determine if any records exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->limit(1)->count() > 0;
    }

    /**
     * Execute an INSERT query.
     *
     * @param array $data
     * @return int|false
     */
    public function insert(array $data): int|false
    {
        $result = $this->wpdb->insert($this->table, $data);
        return $result === false ? false : (int)$this->wpdb->insert_id;
    }

    /**
     * Execute an UPDATE query.
     *
     * @param array $data
     * @return int|false
     */
    public function update(array $data): int|false
    {
        $where = $this->buildWhereConditions();

        return $this->wpdb->update(
            $this->table,
            $data,
            $where['conditions'],
            null,
            null
        );
    }

    /**
     * Execute a DELETE query.
     *
     * @return int|false
     */
    public function delete(): int|false
    {
        $where = $this->buildWhereConditions();

        return $this->wpdb->delete(
            $this->table,
            $where['conditions'],
            null
        );
    }

    /**
     * Count the number of records returned.
     *
     * @return int
     */
    public function count(): int
    {
        $originalSelect = $this->select;
        $this->select = ['COUNT(*) as count'];
        $result = $this->first();
        $this->select = $originalSelect;

        return (int)($result['count'] ?? 0);
    }

    /**
     * Reset the query builder to its initial state.
     *
     * @return $this
     */
    public function reset(): static
    {
        $this->select = ['*'];
        $this->where = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;

        return $this;
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $sql
     * @return array|null
     */
    public function raw(string $sql): ?array
    {
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Build the SELECT query SQL.
     *
     * @return string
     */
    protected function buildSelectQuery(): string
    {
        $query = "SELECT " . implode(', ', $this->select) . " FROM {$this->table}";

        if (!empty($this->where)) {
            $where = $this->buildWhereConditions();
            $query .= " WHERE " . $where['sql'];
        }

        if (!empty($this->orderBy)) {
            $orders = array_map(fn($o) => "{$o['column']} {$o['direction']}", $this->orderBy);
            $query .= " ORDER BY " . implode(', ', $orders);
        }

        if ($this->limit !== null) {
            $query .= " LIMIT {$this->limit}";
            if ($this->offset !== null) {
                $query .= " OFFSET {$this->offset}";
            }
        }

        return $query;
    }

    /**
     * Build the WHERE conditions.
     *
     * @return array{sql: string, conditions: array}
     */
    protected function buildWhereConditions(): array
    {
        $conditions = [];
        $sql = [];

        foreach ($this->where as $w) {
            $sql[] = "{$w['column']} {$w['operator']} " . $this->wpdb->prepare('%s', $w['value']);
            $conditions[$w['column']] = $w['value'];
        }

        return [
            'sql' => implode(' AND ', $sql),
            'conditions' => $conditions,
        ];
    }
}