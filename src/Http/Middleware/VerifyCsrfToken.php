<?php

declare(strict_types=1);

namespace WPJarvis\Core\Http\Middleware;

use WPJarvis\Core\Http\Request;
use WPJarvis\Core\Http\Response;
use WPJarvis\Core\Support\Facades\Response as ResponseFacade;

/**
 * Class VerifyCsrfToken
 *
 * Middleware to verify CSRF tokens on incoming state-changing requests.
 * This helps prevent Cross-Site Request Forgery attacks by validating a token
 * stored in the user's session against one included in the request.
 *
 * @package WPJarvis\Core\Http\Middleware
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * List of URI patterns to exclude from CSRF verification.
     *
     * @var array<int, string>
     */
    protected array $except = [];

    /**
     * Handle an incoming request and verify CSRF protection.
     *
     * @param Request $request The current HTTP request.
     * @param callable(Request): Response $next The next middleware or controller.
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        if (
            $this->isReading($request) ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return $next($request);
        }

        return ResponseFacade::json([
            'error' => 'CSRF token mismatch.',
        ], 419);
    }

    /**
     * Check if the HTTP method is safe for reading (no CSRF check needed).
     *
     * @param Request $request
     * @return bool
     */
    protected function isReading(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    /**
     * Determine if the request URI is in the list of exceptions.
     *
     * @param Request $request
     * @return bool
     */
    protected function inExceptArray(Request $request): bool
    {
        foreach ($this->except as $except) {
            if (stripos(trim($request->path(), '/'), trim($except, '/')) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate if the request token matches the session token.
     *
     * Supports both form-based (`_token`) and header-based (`X-CSRF-TOKEN`) tokens.
     *
     * @param Request $request
     * @return bool
     */
    protected function tokensMatch(Request $request): bool
    {
        // Check Laravel-style token first
        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

        if ($token && isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token)) {
            return true;
        }

        // Fallback: Check WordPress nonce
        if ($request->has('_wpnonce')) {
            return wp_verify_nonce($request->input('_wpnonce'), 'wpjarvis_nonce_action') !== false;
        }

        return false;
    }

    /**
     * Define a list of URIs to exclude from CSRF protection.
     *
     * @param array<int, string> $except
     * @return static
     */
    public function except(array $except): static
    {
        $this->except = $except;

        return $this;
    }
}