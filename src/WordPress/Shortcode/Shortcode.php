<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Shortcode;

use function shortcode_atts;

/**
 * Represents a WordPress shortcode with support for default attributes,
 * view rendering, and optional validation.
 */
class Shortcode
{
    /**
     * The shortcode tag (name).
     *
     * @var string
     */
    protected string $tag;

    /**
     * The callback that handles the shortcode logic.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Default attributes to be merged into the shortcode.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [];

    /**
     * Optional view file or Blade template path.
     *
     * @var string|null
     */
    protected ?string $view = null;

    /**
     * Create a new shortcode instance.
     *
     * @param string $tag The shortcode tag name.
     * @param callable|null $callback Optional callback to handle rendering.
     */
    public function __construct(string $tag, ?callable $callback = null)
    {
        $this->tag = $tag;
        $this->callback = $callback ?: [$this, 'handle'];
    }

    /**
     * Set default attributes for the shortcode.
     *
     * @param array<string, mixed> $defaults
     * @return $this
     */
    public function setDefaults(array $defaults): static
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * Set a view path to use for rendering the shortcode.
     *
     * @param string $view Blade view name or PHP view path.
     * @return $this
     */
    public function setView(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Register the shortcode with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        add_shortcode($this->tag, $this->callback);
    }

    /**
     * Default shortcode handler. Override this method or pass a callback in constructor.
     *
     * @param array<string, mixed> $atts
     * @param string|null $content
     * @return string
     */
    public function handle(array $atts = [], ?string $content = null): string
    {
        $atts = shortcode_atts($this->defaults, $atts, $this->tag);
        $atts = $this->validate($atts);

        if ($this->view) {
            return $this->renderView($atts + ['content' => $content]);
        }

        return 'Override the handle() method or provide a callback for [' . esc_html($this->tag) . '].';
    }

    /**
     * Validate shortcode attributes.
     * Override in subclasses to apply rules.
     *
     * @param array<string, mixed> $atts
     * @return array<string, mixed>
     */
    protected function validate(array $atts): array
    {
        // Placeholder: override to apply validation rules.
        return $atts;
    }

    /**
     * Render a view template using the provided data.
     *
     * @param array<string, mixed> $data
     * @return string
     */
    protected function renderView(array $data): string
    {
        // Blade-style: if using a view system
        if (function_exists('view')) {
            return view($this->view, $data)->render();
        }

        // Fallback: load PHP file if it's a path
        if (is_file($this->view)) {
            return $this->renderPhpView($this->view, $data);
        }

        return '';
    }

    /**
     * Render a raw PHP view file using output buffering.
     *
     * @param string $filePath
     * @param array<string, mixed> $data
     * @return string
     */
    protected function renderPhpView(string $filePath, array $data): string
    {
        ob_start();
        extract($data, EXTR_SKIP);
        include $filePath;
        return ob_get_clean();
    }

    /**
     * Get the shortcode tag.
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }
}