<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Menu;

/**
 * Abstract base class for a WordPress admin menu item.
 * Can be extended for top-level menus or submenus.
 */
abstract class Menu
{
    /**
     * Page title shown in the <title> tag of the admin page.
     *
     * @var string
     */
    protected string $pageTitle;

    /**
     * Menu title shown in the sidebar.
     *
     * @var string
     */
    protected string $menuTitle;

    /**
     * Required capability to view this menu.
     *
     * @var string
     */
    protected string $capability = 'manage_options';

    /**
     * Unique slug used to identify the menu.
     *
     * @var string
     */
    protected string $menuSlug;

    /**
     * Callback function that renders the menu page.
     *
     * @var callable|null
     */
    protected $callback = null;

    /**
     * Dashicons icon or image URL (used for top-level menus).
     *
     * @var string
     */
    protected string $icon = 'dashicons-admin-generic';

    /**
     * Menu position (order in sidebar).
     *
     * @var int|float|null
     */
    protected int|float|null $position = null;

    /**
     * Create a new menu instance.
     *
     * @param string $pageTitle Page <title> and heading text.
     * @param string $menuTitle Label in the admin sidebar.
     * @param string $menuSlug Unique slug for the menu item.
     * @param callable|null $callback Optional callback for content rendering.
     */
    public function __construct(
        string    $pageTitle,
        string    $menuTitle,
        string    $menuSlug,
        ?callable $callback = null
    )
    {
        $this->pageTitle = $pageTitle;
        $this->menuTitle = $menuTitle;
        $this->menuSlug = $menuSlug;
        $this->callback = $callback ?? [$this, 'render'];
    }

    /**
     * Set the required capability to access the menu.
     *
     * @param string $capability
     * @return $this
     */
    public function setCapability(string $capability): static
    {
        $this->capability = $capability;
        return $this;
    }

    /**
     * Set the icon used in the admin sidebar.
     *
     * @param string $icon Dashicons class or image URL.
     * @return $this
     */
    public function setIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the menu's position in the sidebar.
     *
     * @param int|float $position
     * @return $this
     */
    public function setPosition(int|float $position): static
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Default render method if no callback is provided.
     *
     * @return void
     */
    public function render(): void
    {
        echo '<div class="wrap"><h1>' . esc_html($this->menuTitle) . '</h1><p>Menu content goes here.</p></div>';
    }

    /**
     * Register the menu with WordPress.
     * Must be implemented by child classes.
     *
     * @return string The resulting page hook suffix.
     */
    abstract public function register(): string;

    /**
     * Get the menu slug.
     *
     * @return string
     */
    public function getMenuSlug(): string
    {
        return $this->menuSlug;
    }
}