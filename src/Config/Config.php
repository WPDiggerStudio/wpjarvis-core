<?php

declare(strict_types=1);

namespace WPJarvis\Core\Config;

use Illuminate\Config\Repository;

/**
 * Class Config
 *
 * A Laravel-style config wrapper with WordPress option integration and advanced helpers.
 */
class Config
{
    /**
     * Laravel configuration repository.
     */
    protected Repository $repository;

    /**
     * Prefix for WordPress options.
     */
    protected string $prefix = 'wpjarvis_';

    /**
     * Config constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get a configuration value. Fallback to WordPress if not set in memory.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->repository->get($key, null);

        if (is_null($value) && function_exists('get_option')) {
            $optionKey = $this->prefix . str_replace('.', '_', $key);
            $value = get_option($optionKey, $default);
        }

        return $value ?? $default;
    }

    /**
     * Set a configuration value in memory and optionally in WordPress.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->repository->set($key, $value);

        if (function_exists('update_option')) {
            $optionKey = $this->prefix . str_replace('.', '_', $key);
            update_option($optionKey, $value);
        }
    }

    /**
     * Determine if a configuration key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if ($this->repository->has($key)) {
            return true;
        }

        if (function_exists('get_option')) {
            $optionKey = $this->prefix . str_replace('.', '_', $key);
            return get_option($optionKey, null) !== null;
        }

        return false;
    }

    /**
     * Remove a config value from memory and WordPress.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        $this->repository->forget($key);

        if (function_exists('delete_option')) {
            $optionKey = $this->prefix . str_replace('.', '_', $key);
            delete_option($optionKey);
        }
    }

    /**
     * Get all configuration values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->repository->all();
    }

    /**
     * Load configuration values from a file.
     *
     * @param string $path
     * @param string|null $key
     * @return void
     */
    public function load(string $path, ?string $key = null): void
    {
        if (!file_exists($path)) {
            return;
        }

        $config = require $path;

        if (!is_array($config)) {
            return;
        }

        if ($key !== null) {
            $this->repository->set($key, $config);
        } else {
            foreach ($config as $k => $v) {
                $this->repository->set($k, $v);
            }
        }
    }

    /**
     * Merge new config values into an existing key.
     *
     * @param string $key
     * @param array $values
     * @return void
     */
    public function merge(string $key, array $values): void
    {
        $existing = $this->get($key, []);
        if (!is_array($existing)) {
            $existing = [];
        }

        $merged = array_merge($existing, $values);
        $this->set($key, $merged);
    }

    /**
     * Push a value into a config array.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function push(string $key, mixed $value): void
    {
        $array = $this->get($key, []);
        if (!is_array($array)) {
            $array = [];
        }

        $array[] = $value;
        $this->set($key, $array);
    }

    /**
     * Prepend a value into a config array.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function prepend(string $key, mixed $value): void
    {
        $array = $this->get($key, []);
        if (!is_array($array)) {
            $array = [];
        }

        array_unshift($array, $value);
        $this->set($key, $array);
    }

    /**
     * Set the repository instance.
     *
     * @param Repository $repository
     * @return void
     */
    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * Get the underlying repository.
     *
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Set the WordPress option prefix.
     *
     * @param string $prefix
     * @return void
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = rtrim($prefix, '_') . '_';
    }

    /**
     * Get the WordPress prefix in use.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}