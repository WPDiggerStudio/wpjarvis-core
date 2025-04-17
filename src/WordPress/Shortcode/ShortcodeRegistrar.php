<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Shortcode;

use WPJarvis\Core\Container\Container;

/**
 * Registers and manages multiple WordPress shortcodes.
 */
class ShortcodeRegistrar
{
    /**
     * The application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered shortcode instances.
     *
     * @var array<string, Shortcode>
     */
    protected array $shortcodes = [];

    /**
     * Create a new shortcode registrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Add a new shortcode.
     *
     * @param string $tag The shortcode tag name.
     * @param callable|null $callback Optional callback. If null, uses `handle()`.
     * @return Shortcode
     */
    public function add(string $tag, ?callable $callback = null): Shortcode
    {
        $shortcode = new Shortcode($tag, $callback);
        $this->shortcodes[$tag] = $shortcode;

        return $shortcode;
    }

    /**
     * Register all added shortcodes with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->shortcodes as $shortcode) {
            $shortcode->register();
        }
    }

    /**
     * Get all registered shortcode instances.
     *
     * @return array<string, Shortcode>
     */
    public function getShortcodes(): array
    {
        return $this->shortcodes;
    }
}
