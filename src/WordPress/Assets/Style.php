<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

/**
 * Represents a CSS stylesheet asset for WordPress.
 * Supports media targeting, versioning, dependencies, and inline styles.
 */
class Style extends Asset
{
    /**
     * Media type to apply the stylesheet to (e.g. 'all', 'screen', 'print').
     *
     * @var string
     */
    protected string $media = 'all';

    /**
     * Create a new style instance.
     *
     * @param string $handle Unique style handle.
     * @param string $src URL or path to the CSS file.
     * @param array<int, string> $deps Dependency handles.
     * @param string|bool|null $version Style version or false/null.
     * @param string $media Media type (default: 'all').
     */
    public function __construct(
        string           $handle,
        string           $src,
        array            $deps = [],
        string|bool|null $version = null,
        string           $media = 'all'
    )
    {
        parent::__construct($handle, $src, $deps, $version);
        $this->media = $media;
    }

    /**
     * Set the media type for the style.
     *
     * @param string $media
     * @return $this
     */
    public function setMedia(string $media): static
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Register the style with WordPress.
     *
     * @return bool True if the style was successfully registered.
     */
    public function register(): bool
    {
        return wp_register_style(
            $this->handle,
            $this->src,
            $this->deps,
            $this->resolveVersion(),
            $this->media
        );
    }

    /**
     * Enqueue the style in WordPress and apply any inline styles.
     *
     * @return void
     */
    public function enqueue(): void
    {
        wp_enqueue_style($this->handle);

        // Apply inline styles if defined
        $this->applyInlineCode('style');
    }
}
