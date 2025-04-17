<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

/**
 * Represents a JavaScript asset to be registered and enqueued in WordPress.
 * Supports localization, footer placement, and extended attributes like defer and async.
 */
class Script extends Asset
{
    /**
     * Whether to load the script in the footer.
     *
     * @var bool
     */
    protected bool $inFooter = true;

    /**
     * List of localization objects keyed by JavaScript variable name.
     *
     * @var array<string, array>
     */
    protected array $localization = [];

    /**
     * Create a new Script instance.
     *
     * @param string $handle Unique script handle.
     * @param string $src Script source URL.
     * @param array<int, string> $deps List of dependencies.
     * @param string|bool|null $version Script version.
     * @param bool $inFooter Whether to enqueue in footer.
     */
    public function __construct(
        string $handle,
        string $src,
        array $deps = [],
        string|bool|null $version = null,
        bool $inFooter = true
    ) {
        parent::__construct($handle, $src, $deps, $version);
        $this->inFooter = $inFooter;
    }

    /**
     * Set whether to enqueue this script in the footer.
     *
     * @param bool $inFooter
     * @return $this
     */
    public function inFooter(bool $inFooter = true): static
    {
        $this->inFooter = $inFooter;
        return $this;
    }

    /**
     * Add localization data to be made available to the script.
     *
     * @param string $objectName Name of the JS variable/object.
     * @param array<string, mixed> $data Data to pass to JS.
     * @return $this
     */
    public function localize(string $objectName, array $data): static
    {
        $this->localization[$objectName] = $data;
        return $this;
    }

    /**
     * Register the script with WordPress.
     * Also applies localization and translation support.
     *
     * @return bool True if successfully registered.
     */
    public function register(): bool
    {
        $registered = wp_register_script(
            $this->handle,
            $this->src,
            $this->deps,
            $this->resolveVersion(),
            $this->inFooter
        );

        if ($registered) {
            // Add localization data
            foreach ($this->localization as $objectName => $data) {
                wp_localize_script($this->handle, $objectName, $data);
            }

            // Apply translation files (.json)
            $this->applyTranslation();
        }

        return $registered;
    }

    /**
     * Enqueue the script in WordPress.
     * Automatically applies inline code and defer/async attributes.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_script($this->handle);

        // Apply inline script if any
        $this->applyInlineCode('script');
    }
}
