<?php

declare(strict_types=1);

namespace WPJarvis\Core\WordPress\Editor;

/**
 * Manages the customization of the TinyMCE (classic) editor toolbar.
 * Supports adding/removing buttons on the first and second toolbar rows.
 */
class Toolbar
{
    /**
     * Buttons to add to the first toolbar row.
     *
     * @var array<int, string>
     */
    protected array $firstRow = [];

    /**
     * Buttons to add to the second toolbar row.
     *
     * @var array<int, string>
     */
    protected array $secondRow = [];

    /**
     * Buttons to remove from the first toolbar row.
     *
     * @var array<int, string>
     */
    protected array $removeFirstRow = [];

    /**
     * Buttons to remove from the second toolbar row.
     *
     * @var array<int, string>
     */
    protected array $removeSecondRow = [];

    /**
     * Add one or more buttons to the first toolbar row.
     *
     * @param string|array<int, string> $button
     * @return $this
     */
    public function addToFirstRow(string|array $button): static
    {
        $this->firstRow = array_merge($this->firstRow, (array)$button);
        return $this;
    }

    /**
     * Add one or more buttons to the second toolbar row.
     *
     * @param string|array<int, string> $button
     * @return $this
     */
    public function addToSecondRow(string|array $button): static
    {
        $this->secondRow = array_merge($this->secondRow, (array)$button);
        return $this;
    }

    /**
     * Remove one or more buttons from the first toolbar row.
     *
     * @param string|array<int, string> $button
     * @return $this
     */
    public function removeFromFirstRow(string|array $button): static
    {
        $this->removeFirstRow = array_merge($this->removeFirstRow, (array)$button);
        return $this;
    }

    /**
     * Remove one or more buttons from the second toolbar row.
     *
     * @param string|array<int, string> $button
     * @return $this
     */
    public function removeFromSecondRow(string|array $button): static
    {
        $this->removeSecondRow = array_merge($this->removeSecondRow, (array)$button);
        return $this;
    }

    /**
     * Hook the toolbar into WordPress using TinyMCE filters.
     *
     * @return void
     */
    public function register(): void
    {
        add_filter('mce_buttons', [$this, 'filterFirstRow']);
        add_filter('mce_buttons_2', [$this, 'filterSecondRow']);
    }

    /**
     * Filter buttons for the first toolbar row.
     *
     * @param array<int, string> $buttons
     * @return array<int, string>
     */
    public function filterFirstRow(array $buttons): array
    {
        $buttons = array_diff($buttons, $this->removeFirstRow);
        $buttons = array_merge($buttons, $this->firstRow);

        return $buttons;
    }

    /**
     * Filter buttons for the second toolbar row.
     *
     * @param array<int, string> $buttons
     * @return array<int, string>
     */
    public function filterSecondRow(array $buttons): array
    {
        $buttons = array_diff($buttons, $this->removeSecondRow);
        $buttons = array_merge($buttons, $this->secondRow);

        return $buttons;
    }
}
