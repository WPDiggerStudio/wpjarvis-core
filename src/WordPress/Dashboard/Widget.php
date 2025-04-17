<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Dashboard;

/**
 * Represents a WordPress Dashboard widget.
 */
class Widget
{
    /**
     * Unique widget ID.
     *
     * @var string
     */
    protected string $id;

    /**
     * Human-readable title of the widget.
     *
     * @var string
     */
    protected string $title;

    /**
     * Callback to render the widget content.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Optional callback for handling widget controls (settings UI).
     *
     * @var callable|null
     */
    protected $controlCallback = null;

    /**
     * Optional additional arguments for future use (context, priority, etc.).
     *
     * @var array<string, mixed>
     */
    protected array $args = [];

    /**
     * Construct a new Dashboard widget instance.
     *
     * @param string $id Unique ID for the widget.
     * @param string $title Title displayed in the dashboard UI.
     * @param callable|null $callback Function to display widget content.
     * @param callable|null $controlCallback Function to handle widget controls/settings.
     */
    public function __construct(
        string    $id,
        string    $title,
        ?callable $callback = null,
        ?callable $controlCallback = null
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->callback = $callback ?? [$this, 'render'];
        $this->controlCallback = $controlCallback;
    }

    /**
     * Set optional arguments for the widget.
     * Example: context, priority (for future extensibility).
     *
     * @param array<string, mixed> $args
     * @return $this
     */
    public function setArguments(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Default render method.
     * Used if no callback is provided.
     *
     * @return void
     */
    public function render(): void
    {
        echo '<p>Override the render method or provide a custom callback.</p>';
    }

    /**
     * Default control method (settings UI).
     * Used if no control callback is provided.
     *
     * @return void
     */
    public function control(): void
    {
        // Stub method — can be overridden or passed in as a callback.
    }

    /**
     * Register the dashboard widget with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        wp_add_dashboard_widget(
            $this->id,
            $this->title,
            $this->callback,
            $this->controlCallback
        );
    }
}