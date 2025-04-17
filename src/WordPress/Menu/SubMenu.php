<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Menu;

/**
 * Represents a WordPress admin submenu item.
 */
class SubMenu extends Menu
{
    /**
     * The slug of the parent menu item.
     *
     * @var string
     */
    protected string $parentSlug;

    /**
     * Create a new submenu instance.
     *
     * @param string $parentSlug Slug of the parent menu item.
     * @param string $pageTitle Page <title> and heading.
     * @param string $menuTitle Label in sidebar.
     * @param string|null $menuSlug Optional slug; generated from menu title if null.
     * @param callable|null $callback Optional render callback.
     */
    public function __construct(
        string    $parentSlug,
        string    $pageTitle,
        string    $menuTitle,
        ?string   $menuSlug = null,
        ?callable $callback = null
    )
    {
        $menuSlug = $menuSlug ?: $parentSlug . '-' . sanitize_title($menuTitle);

        parent::__construct($pageTitle, $menuTitle, $menuSlug, $callback);
        $this->parentSlug = $parentSlug;
    }

    /**
     * Set the parent menu slug.
     *
     * @param string $parentSlug
     * @return $this
     */
    public function setParentSlug(string $parentSlug): static
    {
        $this->parentSlug = $parentSlug;
        return $this;
    }

    /**
     * Register the submenu with WordPress.
     *
     * @return string The resulting page hook suffix.
     */
    public function register(): string
    {
        return add_submenu_page(
            $this->parentSlug,
            $this->pageTitle,
            $this->menuTitle,
            $this->capability,
            $this->menuSlug,
            $this->callback ?? [$this, 'render']
        );
    }
}