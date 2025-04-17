<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Dashboard;

use WPJarvis\Core\Container\Container;

/**
 * Manages the registration of WordPress dashboard widgets.
 */
class WidgetRegistrar
{
    /**
     * Application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered dashboard widgets.
     *
     * @var array<int, Widget>
     */
    protected array $widgets = [];

    /**
     * Create a new WidgetRegistrar instance.
     *
     * @param Container $app The application container.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Add and register a new dashboard widget instance.
     *
     * @param string $id Unique widget ID.
     * @param string $title Widget display title.
     * @param callable|null $callback Render callback for displaying content.
     * @param callable|null $controlCallback Optional settings/control callback.
     * @return Widget
     */
    public function add(
        string    $id,
        string    $title,
        ?callable $callback = null,
        ?callable $controlCallback = null
    ): Widget
    {
        $widget = new Widget($id, $title, $callback, $controlCallback);
        $this->widgets[] = $widget;

        return $widget;
    }

    /**
     * Register all widgets with WordPress via wp_add_dashboard_widget().
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->widgets as $widget) {
            $widget->register();
        }
    }

    /**
     * Retrieve all registered widgets.
     *
     * @return array<int, Widget>
     */
    public function getWidgets(): array
    {
        return $this->widgets;
    }
}