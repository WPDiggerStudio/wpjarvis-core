<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

/**
 * Abstract base class representing a WordPress asset (script or style).
 * Provides a fluent interface for defining dependencies, versioning,
 * translation support, inline code, and extra attributes.
 */
abstract class Asset
{
    /**
     * The unique handle for the asset.
     *
     * @var string
     */
    protected string $handle;

    /**
     * The source path or URL of the asset.
     *
     * @var string
     */
    protected string $src;

    /**
     * List of dependent asset handles.
     *
     * @var array<int, string>
     */
    protected array $deps = [];

    /**
     * The version of the asset.
     * Can be a string, false (to disable versioning), or null.
     *
     * @var string|bool|null
     */
    protected string|bool|null $version = null;

    /**
     * The text domain used for script translations (optional).
     *
     * @var string|null
     */
    protected ?string $translationDomain = null;

    /**
     * The directory path to the translation files (optional).
     *
     * @var string|null
     */
    protected ?string $translationPath = null;

    /**
     * Optional inline script or style to be added.
     *
     * @var string|null
     */
    protected ?string $inlineCode = null;

    /**
     * Whether to use file modification time for automatic versioning.
     *
     * @var bool
     */
    protected bool $useFileMtime = false;

    /**
     * Whether the asset should be loaded with the "defer" attribute (scripts only).
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * Whether the asset should be loaded with the "async" attribute (scripts only).
     *
     * @var bool
     */
    protected bool $async = false;

    /**
     * Asset constructor.
     *
     * @param string $handle Unique identifier for the asset.
     * @param string $src URL or path to the asset file.
     * @param array<int, string> $deps List of dependencies (other asset handles).
     * @param string|bool|null $version Asset version, false to skip, null to auto.
     */
    public function __construct(
        string           $handle,
        string           $src,
        array            $deps = [],
        string|bool|null $version = null
    )
    {
        $this->handle = $handle;
        $this->src = $src;
        $this->deps = $deps;
        $this->version = $version ?? false;
    }

    /**
     * Set the asset's dependency list.
     *
     * @param array<int, string> $deps
     * @return $this
     */
    public function setDependencies(array $deps): static
    {
        $this->deps = $deps;
        return $this;
    }

    /**
     * Add a single dependency to the asset.
     *
     * @param string $dep
     * @return $this
     */
    public function addDependency(string $dep): static
    {
        if (!in_array($dep, $this->deps, true)) {
            $this->deps[] = $dep;
        }
        return $this;
    }

    /**
     * Set the version of the asset.
     *
     * @param string|bool|null $version
     * @return $this
     */
    public function setVersion(string|bool|null $version): static
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Enable versioning using file modification time (filemtime).
     *
     * @return $this
     */
    public function enableFileVersioning(): static
    {
        $this->useFileMtime = true;
        return $this;
    }

    /**
     * Attach WordPress i18n translation files to a script asset.
     *
     * @param string $textDomain The text domain for the translations.
     * @param string $path Path to the .json translation files (default: 'languages').
     * @return $this
     */
    public function withTranslation(string $textDomain, string $path = 'languages'): static
    {
        $this->translationDomain = $textDomain;
        $this->translationPath = $path;
        return $this;
    }

    /**
     * Attach inline code to be injected alongside the asset.
     *
     * @param string $code JavaScript or CSS content.
     * @return $this
     */
    public function withInlineCode(string $code): static
    {
        $this->inlineCode = $code;
        return $this;
    }

    /**
     * Add the "defer" attribute to this script.
     *
     * @return $this
     */
    public function defer(): static
    {
        $this->defer = true;
        return $this;
    }

    /**
     * Add the "async" attribute to this script.
     *
     * @return $this
     */
    public function async(): static
    {
        $this->async = true;
        return $this;
    }

    /**
     * Get the asset handle.
     *
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * Resolve the version for the asset.
     * If filemtime is enabled and the file exists, it uses that value.
     *
     * @return string|bool|null
     */
    protected function resolveVersion(): string|bool|null
    {
        if ($this->useFileMtime && file_exists($this->src)) {
            return filemtime($this->src) ?: $this->version;
        }

        return $this->version;
    }

    /**
     * Apply script translations (for JS files).
     *
     * @return void
     */
    protected function applyTranslation(): void
    {
        if ($this->translationDomain && function_exists('wp_set_script_translations')) {
            wp_set_script_translations($this->handle, $this->translationDomain, $this->translationPath);
        }
    }

    /**
     * Attach inline JS or CSS after enqueue.
     *
     * @param string $type 'script' or 'style'
     * @return void
     */
    protected function applyInlineCode(string $type = 'script'): void
    {
        if (!$this->inlineCode) {
            return;
        }

        if ($type === 'script') {
            wp_add_inline_script($this->handle, $this->inlineCode);
        } else {
            wp_add_inline_style($this->handle, $this->inlineCode);
        }
    }

    /**
     * Get HTML attributes to be injected into script tag (e.g., defer/async).
     * Should be used with the 'script_loader_tag' filter.
     *
     * @return string
     */
    public function getExtraScriptAttributes(): string
    {
        $attrs = [];

        if ($this->defer) {
            $attrs[] = 'defer';
        }

        if ($this->async) {
            $attrs[] = 'async';
        }

        return implode(' ', $attrs);
    }

    /**
     * Register the asset with WordPress.
     *
     * @return bool True if successfully registered.
     */
    abstract public function register(): bool;

    /**
     * Enqueue the asset with WordPress.
     *
     * @return void
     */
    abstract public function enqueue(): void;
}