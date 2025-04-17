<?php
declare(strict_types=1);

namespace WPJarvis\Core\WordPress\PostType;

/**
 * Represents a WordPress Custom Post Type definition.
 */
class PostType
{
    /**
     * Post type slug.
     */
    protected string $name;

    /**
     * Singular label.
     */
    protected string $singular;

    /**
     * Plural label.
     */
    protected string $plural;

    /**
     * Registration arguments.
     *
     * @var array<string, mixed>
     */
    protected array $args = [];

    /**
     * Constructor.
     *
     * @param string $name Slug of the post type.
     * @param string|null $singular Singular label.
     * @param string|null $plural Plural label.
     */
    public function __construct(string $name, ?string $singular = null, ?string $plural = null)
    {
        $this->name = $name;
        $this->singular = $singular ?? ucfirst($name);
        $this->plural = $plural ?? $this->singular . 's';

        $this->args = [
            'labels' => $this->generateLabels(),
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor'],
        ];
    }

    /**
     * Generate default labels.
     *
     * @return array<string, string>
     */
    protected function generateLabels(): array
    {
        return [
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'add_new' => "Add New",
            'add_new_item' => "Add New {$this->singular}",
            'edit_item' => "Edit {$this->singular}",
            'new_item' => "New {$this->singular}",
            'view_item' => "View {$this->singular}",
            'view_items' => "View {$this->plural}",
            'search_items' => "Search {$this->plural}",
            'not_found' => "No {$this->plural} found",
            'not_found_in_trash' => "No {$this->plural} found in Trash",
            'all_items' => "All {$this->plural}",
            'archives' => "{$this->singular} Archives",
            'menu_name' => $this->plural,
        ];
    }

    /**
     * Set all arguments for registration.
     *
     * @param array<string, mixed> $args
     * @return $this
     */
    public function setArguments(array $args): static
    {
        $this->args = array_merge($this->args, $args);
        return $this;
    }

    /**
     * Set a single argument value.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setArgument(string $key, mixed $value): static
    {
        $this->args[$key] = $value;
        return $this;
    }

    /**
     * Set supported features (e.g., title, editor, thumbnail).
     *
     * @param array<int, string> $features
     * @return $this
     */
    public function withSupports(array $features): static
    {
        return $this->setArgument('supports', $features);
    }

    /**
     * Set menu position.
     *
     * @param int|float $position
     * @return $this
     */
    public function withMenuPosition(int|float $position): static
    {
        return $this->setArgument('menu_position', $position);
    }

    /**
     * Set menu icon.
     *
     * @param string $icon
     * @return $this
     */
    public function withMenuIcon(string $icon): static
    {
        return $this->setArgument('menu_icon', $icon);
    }

    /**
     * Get the post type slug.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the post type arguments.
     *
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->args;
    }
}