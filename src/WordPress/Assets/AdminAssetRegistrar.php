<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Assets;

use WP_Screen;

/**
 * Handles registration and enqueueing of admin-specific assets.
 * Assets are only enqueued on specified admin screens.
 */
class AdminAssetRegistrar extends AssetRegistrar
{
    /**
     * The admin screens where assets should be enqueued.
     *
     * @var array<int, string>
     */
    protected array $screens = [];

    /**
     * Optional capability required to enqueue assets.
     *
     * @var string|null
     */
    protected ?string $requiredCapability = null;

    /**
     * Set the admin screens where assets should be enqueued.
     *
     * Supports wildcard screen IDs (e.g., 'plugin_*').
     *
     * @param array<string>|string $screens One or more screen IDs.
     * @return $this
     */
    public function onScreens(array|string $screens): static
    {
        $this->screens = is_array($screens) ? $screens : [$screens];
        return $this;
    }

    /**
     * Add an individual screen to the list of target screens.
     *
     * @param string $screen Screen ID (e.g., 'toplevel_page_my_plugin').
     * @return $this
     */
    public function addScreen(string $screen): static
    {
        if (!in_array($screen, $this->screens, true)) {
            $this->screens[] = $screen;
        }
        return $this;
    }

    /**
     * Restrict asset enqueueing to users with a specific capability.
     *
     * @param string $capability
     * @return $this
     */
    public function requireCapability(string $capability): static
    {
        $this->requiredCapability = $capability;
        return $this;
    }

    /**
     * Enqueue assets if the current screen matches one of the target screens
     * and user has the required capability (if set).
     *
     * @param string $group The current admin page hook suffix.
     * @return void
     */
    public function enqueue(string|null $group = ''): void
    {
        if (
            ($this->requiredCapability === null || current_user_can($this->requiredCapability)) &&
            (empty($this->screens) || $this->shouldEnqueue($group))
        ) {
            // Optional logging or action hook for monitoring enqueue events
            do_action('wpjarvis.assets.admin_enqueueing', $this->screens, $group);
            parent::enqueue();
        }
    }

    /**
     * Determine whether assets should be enqueued on the current screen.
     * Supports wildcards like 'plugin_*'.
     *
     * @param string $hookSuffix The current admin page hook suffix.
     * @return bool True if assets should be enqueued, false otherwise.
     */
    protected function shouldEnqueue(string $hookSuffix): bool
    {
        $screen = get_current_screen();

        if (!$screen instanceof WP_Screen) {
            return false;
        }

        foreach ($this->screens as $screenId) {
            // Match exact or wildcard pattern
            if (
                $screen->id === $screenId ||
                $hookSuffix === $screenId ||
                fnmatch($screenId, $screen->id) ||
                fnmatch($screenId, $hookSuffix)
            ) {
                return true;
            }
        }

        return false;
    }
}