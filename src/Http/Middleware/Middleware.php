<?php

declare(strict_types=1);

namespace WPJarvis\Core\Http\Middleware;

use WPJarvis\Core\Http\Request;
use WPJarvis\Core\Http\Response;

/**
 * Class Middleware
 *
 * Base abstract class for creating middleware components.
 * Middleware are used to inspect or modify requests before reaching the controller,
 * and responses before being sent to the client.
 *
 * Usage:
 * Extend this class and implement the `handle` method.
 *
 * @package WPJarvis\Core\Http\Middleware
 */
abstract class Middleware
{
    /**
     * Handle an incoming HTTP request.
     *
     * This method should perform logic such as authentication,
     * logging, validation, etc., and then either:
     * - return a Response to short-circuit the pipeline, or
     * - call $next($request) to continue.
     *
     * @param Request $request The current request instance.
     * @param callable(Request): Response $next The next middleware or controller.
     * @return Response
     */
    abstract public function handle(Request $request, callable $next): Response;
}
