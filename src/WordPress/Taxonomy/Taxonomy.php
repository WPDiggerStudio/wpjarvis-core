<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Taxonomy;

/**
 * Represents a WordPress taxonomy with a fluent API for configuration.
 */
class Taxonomy
{
    /**
     * The unique taxonomy slug.
     *
     * @var string
     */
    protected string $name;

    /**
     * The singular label.
     *
     * @var string
     */
    protected string $singular;

    /**
     * The plural label.
     *
     * @var string
     */
    protected string $plural;

    /**
     * List of post types the taxonomy applies to.
     *
     * @var array<int, string>
     */
    protected array $postTypes = [];

    /**
     * Taxonomy registration arguments.
     *
     * @var array<string, mixed>
     */
    protected array $args = [];

    /**
     * Create a new taxonomy instance.
     *
     * @param string $name Unique taxonomy key.
     * @param string|null $singular Singular label.
     * @param string|null $plural Plural label.
     * @param array<int, string> $postTypes List of post types this taxonomy applies to.
     */
    public function __construct(
        string  $name,
        ?string $singular = null,
        ?string $plural = null,
        array   $postTypes = []
    )
    {
        $this->name = $name;
        $this->singular = $singular ?? ucfirst($name);
        $this->plural = $plural ?? $this->singular . 's';
        $this->postTypes = $postTypes;

        $this->args = [
            'labels' => $this->generateLabels(),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'query_var' => true,
        ];
    }

    /**
     * Generate default taxonomy labels.
     *
     * @return array<string, string>
     */
    protected function generateLabels(): array
    {
        return [
            'name' => $this->plural,
            'singular_name' => $this->singular,
            'search_items' => "Search {$this->plural}",
            'all_items' => "All {$this->plural}",
            'parent_item' => "Parent {$this->singular}",
            'parent_item_colon' => "Parent {$this->singular}:",
            'edit_item' => "Edit {$this->singular}",
            'update_item' => "Update {$this->singular}",
            'add_new_item' => "Add New {$this->singular}",
            'new_item_name' => "New {$this->singular} Name",
            'menu_name' => $this->plural,
        ];
    }

    /**
     * Override or add custom taxonomy arguments.
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
     * Set a specific argument key/value.
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
     * Set the post types this taxonomy applies to.
     *
     * @param string|array<int, string> $postTypes
     * @return $this
     */
    public function setPostTypes(string|array $postTypes): static
    {
        $this->postTypes = is_array($postTypes) ? $postTypes : [$postTypes];
        return $this;
    }

    /**
     * Enable or disable hierarchical behavior.
     *
     * @param bool $value
     * @return $this
     */
    public function hierarchical(bool $value = true): static
    {
        return $this->setArgument('hierarchical', $value);
    }

    /**
     * Enable or disable visibility in the REST API.
     *
     * @param bool $value
     * @return $this
     */
    public function showInRest(bool $value = true): static
    {
        return $this->setArgument('show_in_rest', $value);
    }

    /**
     * Get the taxonomy name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the list of post types this taxonomy is registered for.
     *
     * @return array<int, string>
     */
    public function getPostTypes(): array
    {
        return $this->postTypes;
    }

    /**
     * Get the arguments used for registration.
     *
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->args;
    }
}