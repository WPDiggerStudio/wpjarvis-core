<?php
declare(strict_types=1);

namespace WPJarvis\Core\Foundation\ConfigCache;

use WPJarvis\Core\Foundation\Application;

/**
 * Class ConfigCacheManager
 *
 * Handles caching of loaded configuration for performance.
 * Similar to Laravel's `config:cache` and `config:clear`.
 *
 * @package WPJarvis\Core\Foundation\ConfigCache
 */
class ConfigCacheManager
{
    /**
     * Application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Cached config file path.
     *
     * @var string
     */
    protected string $cachePath;

    /**
     * ConfigCacheManager constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->cachePath = $app->basePath('bootstrap/cache/config.php');
    }

    /**
     * Determine if the config cache exists.
     *
     * @return bool
     */
    public function isCached(): bool
    {
        return file_exists($this->cachePath);
    }

    /**
     * Load and return cached configuration.
     *
     * @return array
     */
    public function getCached(): array
    {
        return require $this->cachePath;
    }

    /**
     * Cache the given configuration to disk.
     *
     * @param array $config
     * @return void
     */
    public function cache(array $config): void
    {
        if (!is_dir(dirname($this->cachePath))) {
            mkdir(dirname($this->cachePath), 0755, true);
        }

        $export = var_export($config, true);
        file_put_contents($this->cachePath, "<?php return {$export};");
    }

    /**
     * Clear the cached configuration file.
     *
     * @return void
     */
    public function clear(): void
    {
        if ($this->isCached()) {
            unlink($this->cachePath);
        }
    }

    /**
     * Get the full path to the config cache file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->cachePath;
    }
}
