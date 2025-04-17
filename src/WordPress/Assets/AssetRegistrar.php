<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

use WPJarvis\Core\Container\Container;

/**
 * Registers and enqueues WordPress script and style assets,
 * with support for groups, priority, and environment conditions.
 */
class AssetRegistrar
{
    /**
     * The application container.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered script assets.
     *
     * @var array<string, array<int, array{asset: Script, priority: int, group: string, env: string|null}>>
     */
    protected array $scripts = [];

    /**
     * Registered style assets.
     *
     * @var array<string, array<int, array{asset: Style, priority: int, group: string, env: string|null}>>
     */
    protected array $styles = [];

    /**
     * Create a new asset registrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register a new script.
     *
     * @param string $handle
     * @param string $src
     * @param array<int, string> $deps
     * @param string|bool|null $version
     * @param bool $inFooter
     * @param string $group Group name (e.g. 'admin', 'frontend')
     * @param int $priority Load order
     * @param string|null $env Required environment (e.g., 'production', null = all)
     * @return Script
     */
    public function addScript(
        string           $handle,
        string           $src,
        array            $deps = [],
        string|bool|null $version = null,
        bool             $inFooter = true,
        string           $group = 'default',
        int              $priority = 10,
        ?string          $env = null
    ): Script
    {
        $script = new Script($handle, $src, $deps, $version, $inFooter);

        $this->scripts[$group][$priority][] = [
            'asset' => $script,
            'priority' => $priority,
            'group' => $group,
            'env' => $env
        ];

        return $script;
    }

    /**
     * Register a new style.
     *
     * @param string $handle
     * @param string $src
     * @param array<int, string> $deps
     * @param string|bool|null $version
     * @param string $media
     * @param string $group
     * @param int $priority
     * @param string|null $env
     * @return Style
     */
    public function addStyle(
        string           $handle,
        string           $src,
        array            $deps = [],
        string|bool|null $version = null,
        string           $media = 'all',
        string           $group = 'default',
        int              $priority = 10,
        ?string          $env = null
    ): Style
    {
        $style = new Style($handle, $src, $deps, $version, $media);

        $this->styles[$group][$priority][] = [
            'asset' => $style,
            'priority' => $priority,
            'group' => $group,
            'env' => $env
        ];

        return $style;
    }

    /**
     * Register all assets (scripts and styles) by group.
     *
     * @param string|null $group Optional group filter.
     * @return void
     */
    public function register(?string $group = null): void
    {
        $this->processAssets($this->scripts, 'register', $group);
        $this->processAssets($this->styles, 'register', $group);
    }

    /**
     * Enqueue all assets (scripts and styles) by group.
     *
     * @param string|null $group Optional group filter.
     * @return void
     */
    public function enqueue(?string $group = null): void
    {
        $this->processAssets($this->scripts, 'enqueue', $group);
        $this->processAssets($this->styles, 'enqueue', $group);
    }

    /**
     * Process asset actions by group and priority.
     *
     * @param array<string, array<int, array{asset: Asset, priority: int, group: string, env: string|null}>> $assets
     * @param string $method
     * @param string|null $group
     * @return void
     */
    protected function processAssets(array $assets, string $method, ?string $group = null): void
    {
        $env = defined('WPJARVIS_ENV') ? WPJARVIS_ENV : null;

        foreach ($assets as $assetGroup => $prioritized) {
            if ($group && $group !== $assetGroup) {
                continue;
            }

            ksort($prioritized);

            foreach ($prioritized as $items) {
                foreach ($items as $entry) {
                    if ($entry['env'] === null || $entry['env'] === $env) {
                        $entry['asset']->{$method}();
                    }
                }
            }
        }
    }

    /**
     * Get all registered scripts grouped.
     *
     * @return array<string, array>
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * Get all registered styles grouped.
     *
     * @return array<string, array>
     */
    public function getStyles(): array
    {
        return $this->styles;
    }
}