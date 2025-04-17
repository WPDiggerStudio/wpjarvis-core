<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Metabox;

use WPJarvis\Core\Container\Container;

/**
 * Handles registration and saving of metaboxes in the WordPress admin.
 */
class MetaboxRegistrar
{
    /**
     * The application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Array of registered metabox instances.
     *
     * @var array<int, Metabox>
     */
    protected array $metaboxes = [];

    /**
     * Create a new MetaboxRegistrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register a new metabox instance and return it for chaining.
     *
     * @param string $id Unique ID for the metabox.
     * @param string $title Title shown in the UI.
     * @param string|array<int, string> $screen Post type(s) or screen identifiers.
     * @param callable|null $callback Optional render callback.
     * @return Metabox
     */
    public function add(
        string $id,
        string $title,
        string|array $screen,
        ?callable $callback = null
    ): Metabox {
        $metabox = new Metabox($id, $title, $screen, $callback);
        $this->metaboxes[] = $metabox;

        return $metabox;
    }

    /**
     * Register all added metaboxes with WordPress.
     *
     * @return void
     */
    public function registerMetaboxes(): void
    {
        foreach ($this->metaboxes as $metabox) {
            $metabox->register();
        }
    }

    /**
     * Save metaboxes during post save.
     * Automatically skips autosave and filters by screen.
     *
     * @param int $postId
     * @return void
     */
    public function saveMetaboxes(int $postId): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $postType = get_post_type($postId);

        foreach ($this->metaboxes as $metabox) {
            $screen = $metabox->getScreen();

            if ((is_array($screen) && in_array($postType, $screen, true)) || $screen === $postType) {
                $metabox->save($postId);
            }
        }
    }

    /**
     * Get all registered metabox instances.
     *
     * @return array<int, Metabox>
     */
    public function getMetaboxes(): array
    {
        return $this->metaboxes;
    }
}
