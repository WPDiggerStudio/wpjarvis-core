<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Block;

/**
 * Class Block
 *
 * Represents a Gutenberg block definition for WordPress.
 * Supports namespace, attributes, scripts, styles, categories, and render callbacks.
 */
class Block
{
    /**
     * Block name (without namespace).
     *
     * @var string
     */
    protected string $name;

    /**
     * Block namespace.
     *
     * @var string
     */
    protected string $namespace = 'wpjarvis';

    /**
     * Arguments for register_block_type().
     *
     * @var array<string, mixed>
     */
    protected array $args = [];

    /**
     * Render callback function.
     *
     * @var callable|null
     */
    protected $renderCallback = null;

    /**
     * Construct a new Block instance.
     *
     * @param string $name Block name (without namespace).
     * @param callable|null $renderCallback Optional render callback.
     */
    public function __construct(string $name, callable $renderCallback = null)
    {
        $this->name = $name;
        $this->renderCallback = $renderCallback ?? [$this, 'render'];

        $this->args = [
            'render_callback' => $this->renderCallback,
        ];
    }

    /**
     * Set the block namespace.
     *
     * @param string $namespace
     * @return $this
     */
    public function setNamespace(string $namespace): static
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Set block attributes.
     *
     * @param array<string, mixed> $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->args['attributes'] = $attributes;
        return $this;
    }

    /**
     * Set the editor script handle.
     *
     * @param string $handle
     * @return $this
     */
    public function setEditorScript(string $handle): static
    {
        $this->args['editor_script'] = $handle;
        return $this;
    }

    /**
     * Set the editor style handle.
     *
     * @param string $handle
     * @return $this
     */
    public function setEditorStyle(string $handle): static
    {
        $this->args['editor_style'] = $handle;
        return $this;
    }

    /**
     * Set the frontend style handle.
     *
     * @param string $handle
     * @return $this
     */
    public function setStyle(string $handle): static
    {
        $this->args['style'] = $handle;
        return $this;
    }

    /**
     * Set the block category (e.g., 'common', 'formatting').
     *
     * @param string $category
     * @return $this
     */
    public function setCategory(string $category): static
    {
        $this->args['category'] = $category;
        return $this;
    }

    /**
     * Set additional block supports.
     *
     * @param array<string, mixed> $supports
     * @return $this
     */
    public function setSupports(array $supports): static
    {
        $this->args['supports'] = $supports;
        return $this;
    }

    /**
     * Set block icon.
     *
     * @param string|array $icon
     * @return $this
     */
    public function setIcon(string|array $icon): static
    {
        $this->args['icon'] = $icon;
        return $this;
    }

    /**
     * Set keywords to improve block searchability.
     *
     * @param array<int, string> $keywords
     * @return $this
     */
    public function setKeywords(array $keywords): static
    {
        $this->args['keywords'] = $keywords;
        return $this;
    }

    /**
     * Set custom render callback.
     *
     * @param callable $callback
     * @return $this
     */
    public function setRenderCallback(callable $callback): static
    {
        $this->renderCallback = $callback;
        $this->args['render_callback'] = $callback;
        return $this;
    }

    /**
     * Default render method.
     *
     * @param array<string, mixed> $attributes
     * @param string $content
     * @return string
     */
    public function render(array $attributes, string $content = ''): string
    {
        return '<div class="wpjarvis-block">[Block output not defined]</div>';
    }

    /**
     * Register the block with WordPress.
     *
     * @return bool True if successful, false otherwise.
     */
    public function register(): bool
    {
        if (!function_exists('register_block_type')) {
            return false;
        }

        return register_block_type($this->getFullName(), $this->args);
    }

    /**
     * Get the full block name including namespace.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->namespace}/{$this->name}";
    }
}