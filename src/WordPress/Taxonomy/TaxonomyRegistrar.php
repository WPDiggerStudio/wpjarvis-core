<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Taxonomy;

use WPJarvis\Core\Container\Container;

/**
 * Handles the registration of multiple custom taxonomies.
 */
class TaxonomyRegistrar
{
    /**
     * The application container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Registered taxonomy definitions.
     *
     * @var array<string, Taxonomy>
     */
    protected array $taxonomies = [];

    /**
     * Create a new taxonomy registrar instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Create and register a new Taxonomy instance.
     *
     * @param string $name Taxonomy slug.
     * @param string|null $singular Singular label.
     * @param string|null $plural Plural label.
     * @param array<int, string> $postTypes Post types to attach this taxonomy to.
     * @return Taxonomy
     */
    public function taxonomy(
        string  $name,
        ?string $singular = null,
        ?string $plural = null,
        array   $postTypes = []
    ): Taxonomy
    {
        $taxonomy = new Taxonomy($name, $singular, $plural, $postTypes);
        $this->taxonomies[$name] = $taxonomy;

        return $taxonomy;
    }

    /**
     * Register all stored taxonomies with WordPress.
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->taxonomies as $taxonomy) {
            register_taxonomy(
                $taxonomy->getName(),
                $taxonomy->getPostTypes(),
                $taxonomy->getArguments()
            );
        }
    }

    /**
     * Get all registered taxonomy instances.
     *
     * @return array<string, Taxonomy>
     */
    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }
}
