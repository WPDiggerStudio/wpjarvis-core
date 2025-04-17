<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

/**
 * Manages asset registration and enqueueing on the WordPress frontend.
 * Supports conditional logic for where assets should be loaded.
 */
class FrontendAssetRegistrar extends AssetRegistrar
{
    /**
     * Conditions to evaluate before enqueueing assets.
     *
     * @var array<int, callable>
     */
    protected array $conditions = [];

    /**
     * Add a conditional callback to determine enqueue eligibility.
     *
     * @param callable(): bool $condition
     * @return $this
     */
    public function when(callable $condition): static
    {
        $this->conditions[] = $condition;
        return $this;
    }

    /**
     * Enqueue assets only on the front page.
     *
     * @return $this
     */
    public function onFrontPage(): static
    {
        return $this->when(static fn(): bool => is_front_page());
    }

    /**
     * Enqueue assets only on singular post/pages.
     *
     * @param string|array<string>|null $postTypes Optional post type(s) to restrict.
     * @return $this
     */
    public function onSingular(string|array|null $postTypes = null): static
    {
        return $this->when(static fn(): bool => is_singular($postTypes));
    }

    /**
     * Enqueue assets only on archive pages.
     *
     * @param string|array<string>|null $postTypes Optional post type(s) to restrict.
     * @return $this
     */
    public function onArchive(string|array|null $postTypes = null): static
    {
        return $this->when(static fn(): bool => is_archive() && ($postTypes === null || is_post_type_archive($postTypes))
        );
    }

    /**
     * Enqueue all registered frontend assets if conditions pass.
     *
     * @return void
     */
    public function enqueue(): void
    {
        if ($this->shouldEnqueue()) {
            parent::enqueue();
        }
    }

    /**
     * Determine whether to enqueue assets based on provided conditions.
     *
     * @return bool True if any condition passes or none set.
     */
    protected function shouldEnqueue(): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            if (call_user_func($condition) === true) {
                return true;
            }
        }

        return false;
    }
}