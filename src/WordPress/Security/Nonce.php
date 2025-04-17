<?php

declare(strict_types=1);

namespace WPJarvis\Security;

/**
 * Provides a static API for working with WordPress nonces.
 */
class Nonce
{
    /**
     * Generate a nonce string for a specific action.
     *
     * @param string|int $action The action name (or default -1).
     * @return string The generated nonce string.
     */
    public static function generate(string|int $action = -1): string
    {
        return wp_create_nonce($action);
    }

    /**
     * Verify the validity of a nonce.
     *
     * @param string $nonce The nonce string to verify.
     * @param string|int $action The action name to verify against.
     * @return int|false 1 (0–12 hrs valid), 2 (12–24 hrs valid), or false (invalid).
     */
    public static function verify(string $nonce, string|int $action = -1): int|false
    {
        return wp_verify_nonce($nonce, $action);
    }

    /**
     * Output or return a hidden nonce input field for a form.
     *
     * @param string|int $action Action name.
     * @param string $name The input name (default: '_wpnonce').
     * @param bool $referer Whether to include the referer field.
     * @param bool $echo Whether to echo (true) or return (false) the field.
     * @return string|null If $echo is false, returns the field HTML.
     */
    public static function field(
        string|int $action = -1,
        string     $name = '_wpnonce',
        bool       $referer = true,
        bool       $echo = true
    ): string|null
    {
        return wp_nonce_field($action, $name, $referer, $echo);
    }

    /**
     * Add a nonce to a URL.
     *
     * @param string $actionUrl The base URL.
     * @param string|int $action The action name.
     * @param string $name The nonce name used as query arg.
     * @return string The URL with appended nonce query parameter.
     */
    public static function url(string $actionUrl, string|int $action = -1, string $name = '_wpnonce'): string
    {
        return wp_nonce_url($actionUrl, $action, $name);
    }

    /**
     * Validate a nonce for admin requests (usually used in POST/GET actions).
     *
     * @param string|int $action Action name.
     * @param string $queryArg The query arg containing the nonce (default: '_wpnonce').
     * @return int|false Same as verify: 1, 2, or false.
     */
    public static function checkAdminReferer(string|int $action = -1, string $queryArg = '_wpnonce'): int|false
    {
        return check_admin_referer($action, $queryArg);
    }

    /**
     * Validate a nonce for AJAX requests.
     *
     * @param string|int $action Action name.
     * @param string $queryArg Query param name containing the nonce.
     * @param bool $die Whether to terminate request if nonce is invalid.
     * @return int|false Same as verify: 1, 2, or false.
     */
    public static function checkAjaxReferer(
        string|int $action = -1,
        string     $queryArg = '_wpnonce',
        bool       $die = true
    ): int|false
    {
        return check_ajax_referer($action, $queryArg, $die);
    }
}
