<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Block;

use WPJarvis\Core\Container\Container;

/**
 * Registers Gutenberg blocks and block categories with WordPress.
 */
class BlockRegistrar
{
    /**
     * The application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered blocks.
     *
     * @var array<string, Block>
     */
    protected array $blocks = [];

    /**
     * Registered custom block categories.
     *
     * @var array<int, array{slug: string, title: string, icon: string|null}>
     */
    protected array $categories = [];

    /**
     * Create a new BlockRegistrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        // Register filter to append custom block categories
        add_filter('block_categories_all', [$this, 'registerCategories']);
    }

    /**
     * Add and register a new block instance.
     *
     * @param string $name Block name (without namespace).
     * @param callable|null $renderCallback Optional render callback.
     * @return Block
     */
    public function add(string $name, ?callable $renderCallback = null): Block
    {
        $block = new Block($name, $renderCallback);
        $this->blocks[$block->getFullName()] = $block;

        return $block;
    }

    /**
     * Add a custom block category.
     *
     * @param string $slug Unique category slug.
     * @param string $title Human-readable category title.
     * @param string|null $icon Optional Dashicon or SVG icon.
     * @return $this
     */
    public function addCategory(string $slug, string $title, ?string $icon = null): static
    {
        $this->categories[] = [
            'slug' => $slug,
            'title' => $title,
            'icon' => $icon,
        ];

        return $this;
    }

    /**
     * Register all blocks added to the registrar.
     *
     * @return void
     */
    public function register(): void
    {
        if (!function_exists('register_block_type')) {
            return;
        }

        foreach ($this->blocks as $block) {
            $block->register();
        }
    }

    /**
     * WordPress filter callback to append custom block categories.
     *
     * @param array<int, array> $categories Default block categories.
     * @return array<int, array>
     */
    public function registerCategories(array $categories): array
    {
        return array_merge(
            $categories,
            array_map(fn(array $category): array => [
                'slug' => $category['slug'],
                'title' => $category['title'],
                'icon' => $category['icon'] ?? null,
            ], $this->categories)
        );
    }

    /**
     * Get all registered block instances.
     *
     * @return array<string, Block>
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }
}
