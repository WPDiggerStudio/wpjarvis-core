<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Menu;

use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers admin menus and submenus with WordPress.
 * Menus must extend the base Menu class.
 */
class MenuServiceProvider extends ServiceProvider
{
    /**
     * Menu instances to be registered with WordPress.
     *
     * @var array<int, Menu>
     */
    protected array $menus = [];

    /**
     * Register services with the container.
     * (No bindings needed for this provider.)
     *
     * @return void
     */
    public function register(): void
    {
        // Reserved for future bindings if needed.
    }

    /**
     * Bootstrap the menu system during the WordPress admin phase.
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerMenus']);
    }

    /**
     * Add a menu (AdminMenu or SubMenu) to the provider.
     *
     * @param Menu $menu
     * @return $this
     */
    public function addMenu(Menu $menu): static
    {
        $this->menus[] = $menu;
        return $this;
    }

    /**
     * Register all configured menus with WordPress.
     *
     * @return void
     */
    public function registerMenus(): void
    {
        foreach ($this->menus as $menu) {
            $menu->register();
        }
    }
}