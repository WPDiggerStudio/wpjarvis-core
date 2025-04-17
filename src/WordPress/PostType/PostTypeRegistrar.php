<?php


declare(strict_types=1);

namespace WPJarvis\Core\WordPress\PostType;

use WPJarvis\Core\Container\Container;

/**
 * Handles the registration of multiple custom post types.
 */
class PostTypeRegistrar
{
    /**
     * The application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered custom post types.
     *
     * @var array<string, PostType>
     */
    protected array $postTypes = [];

    /**
     * Create a new registrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create and register a new PostType definition.
     *
     * @param string $name Slug of the post type.
     * @param string|null $singular
     * @param string|null $plural
     * @return PostType
     */
    public function postType(string $name, ?string $singular = null, ?string $plural = null): PostType
    {
        $postType = new PostType($name, $singular, $plural);
        $this->postTypes[$name] = $postType;

        return $postType;
    }

    /**
     * Register all post types with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->postTypes as $postType) {
            register_post_type($postType->getName(), $postType->getArguments());
        }
    }

    /**
     * Get all registered post types.
     *
     * @return array<string, PostType>
     */
    public function getPostTypes(): array
    {
        return $this->postTypes;
    }
}
