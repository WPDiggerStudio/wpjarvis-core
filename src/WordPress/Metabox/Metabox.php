<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Metabox;

/**
 * Represents a single WordPress admin metabox.
 * Used to display custom fields, content, or settings in the post editor.
 */
class Metabox
{
    /**
     * Unique metabox ID.
     *
     * @var string
     */
    protected string $id;

    /**
     * The metabox title (header).
     *
     * @var string
     */
    protected string $title;

    /**
     * One or more post types or screens this metabox should appear on.
     *
     * @var string|array<int, string>
     */
    protected string|array $screen;

    /**
     * Metabox display context: 'normal', 'side', or 'advanced'.
     *
     * @var string
     */
    protected string $context = 'advanced';

    /**
     * Metabox priority: 'high', 'core', 'default', or 'low'.
     *
     * @var string
     */
    protected string $priority = 'default';

    /**
     * Callback to render metabox content.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Optional additional data passed to the callback.
     *
     * @var array<string, mixed>
     */
    protected array $callbackArgs = [];

    /**
     * Create a new metabox instance.
     *
     * @param string $id Unique ID.
     * @param string $title Title shown in the editor.
     * @param string|array<int, string> $screen Post type(s) or screen IDs.
     * @param callable|null $callback Render callback or null to use default.
     */
    public function __construct(
        string       $id,
        string       $title,
        string|array $screen,
        ?callable    $callback = null
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->screen = $screen;
        $this->callback = $callback ?? [$this, 'render'];
    }

    /**
     * Set the display context.
     *
     * @param string $context 'normal', 'side', or 'advanced'.
     * @return $this
     */
    public function setContext(string $context): static
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Set the priority of the metabox.
     *
     * @param string $priority 'high', 'core', 'default', or 'low'.
     * @return $this
     */
    public function setPriority(string $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Set additional arguments passed to the callback.
     *
     * @param array<string, mixed> $args
     * @return $this
     */
    public function setCallbackArgs(array $args): static
    {
        $this->callbackArgs = $args;
        return $this;
    }

    /**
     * Default render method for the metabox.
     * Override or pass your own callback for real content.
     *
     * @param \WP_Post $post
     * @param array<string, mixed> $metabox
     * @return void
     */
    public function render(\WP_Post $post, array $metabox): void
    {
        echo '<p>Override the render() method or pass a callback.</p>';
    }

    /**
     * Register this metabox with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        add_meta_box(
            $this->id,
            $this->title,
            $this->callback,
            $this->screen,
            $this->context,
            $this->priority,
            $this->callbackArgs
        );
    }

    /**
     * Hook to save metabox data.
     * Meant to be overridden in subclasses or user code.
     *
     * @param int $postId
     * @return void
     */
    public function save(int $postId): void
    {
        // Intentionally blank for override.
    }

    /**
     * Get the metabox ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the screen(s) the metabox is attached to.
     *
     * @return string|array<int, string>
     */
    public function getScreen(): string|array
    {
        return $this->screen;
    }
}