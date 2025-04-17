<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Menu;

/**
 * Represents a top-level WordPress admin menu item.
 * Fluent, flexible, and extendable for modern WordPress plugin development.
 */
class AdminMenu extends Menu
{
    /**
     * Page title shown in browser tab and page heading.
     *
     * @var string
     */
    protected string $pageTitle = '';

    /**
     * Menu label shown in the admin sidebar.
     *
     * @var string
     */
    protected string $menuTitle = '';

    /**
     * Required capability to access this menu.
     *
     * @var string
     */
    protected string $capability = 'manage_options';

    /**
     * Unique slug for identifying this menu item.
     *
     * @var string
     */
    protected string $menuSlug = '';

    /**
     * Callback to render the content of the menu page.
     *
     * @var callable|null
     */
    protected $callback = null;

    /**
     * Icon for the menu (Dashicon class or image URL).
     *
     * @var string
     */
    protected string $icon = 'dashicons-admin-generic';

    /**
     * Position of the menu in the admin sidebar.
     *
     * @var int|float|null
     */
    protected int|float|null $position = null;

    /**
     * Register the admin menu with WordPress.
     *
     * @return string The resulting page hook suffix.
     */
    public function register(): string
    {
        return add_menu_page(
            $this->pageTitle,
            $this->menuTitle,
            $this->capability,
            $this->menuSlug,
            $this->callback ?? [$this, 'render'],
            $this->icon,
            $this->position
        );
    }

    /**
     * Default render callback if none is provided.
     *
     * @return void
     */
    public function render(): void
    {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html($this->menuTitle) . '</h1>';
        echo '<p>This is the default admin menu content. Override this by providing a custom callback.</p>';
        echo '</div>';
    }

    // ─── Fluent Configuration ─────────────────────────────────────

    /**
     * Set the page title shown in the browser tab and page heading.
     *
     * @param string $title
     * @return $this
     */
    public function withPageTitle(string $title): static
    {
        $this->pageTitle = $title;
        return $this;
    }

    /**
     * Set the label shown in the admin sidebar.
     *
     * @param string $title
     * @return $this
     */
    public function withMenuTitle(string $title): static
    {
        $this->menuTitle = $title;
        return $this;
    }

    /**
     * Set the required user capability to access this menu.
     *
     * @param string $capability
     * @return $this
     */
    public function withCapability(string $capability): static
    {
        $this->capability = $capability;
        return $this;
    }

    /**
     * Set the unique slug identifier for this menu.
     *
     * @param string $slug
     * @return $this
     */
    public function withSlug(string $slug): static
    {
        $this->menuSlug = $slug;
        return $this;
    }

    /**
     * Set the callback responsible for rendering this menu page.
     *
     * @param callable $callback
     * @return $this
     */
    public function withCallback(callable $callback): static
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Set the icon shown in the admin sidebar.
     *
     * @param string $icon
     * @return $this
     */
    public function withIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set the position of the menu in the admin sidebar.
     *
     * @param int|float $position
     * @return $this
     */
    public function withPosition(int|float $position): static
    {
        $this->position = $position;
        return $this;
    }
}