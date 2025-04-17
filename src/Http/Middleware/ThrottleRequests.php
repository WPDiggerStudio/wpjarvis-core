<?php
declare(strict_types=1);

namespace WPJarvis\Core\Http\Middleware;

use WPJarvis\Core\Http\Request;
use WPJarvis\Core\Http\Response;
use WPJarvis\Core\Support\Facades\Response as ResponseFacade;

/**
 * Class ThrottleRequests
 *
 * Middleware to throttle incoming HTTP requests based on IP and request path.
 * Limits the number of requests within a specified time window (decay).
 *
 * @package WPJarvis\Core\Http\Middleware
 */
class ThrottleRequests extends Middleware
{
    /**
     * The maximum number of allowed attempts within the decay period.
     *
     * @var int
     */
    protected int $maxAttempts = 60;

    /**
     * The number of seconds to wait before resetting the attempt count.
     *
     * @var int
     */
    protected int $decaySeconds = 60;

    /**
     * Handle an incoming request and apply rate limiting logic.
     *
     * @param Request $request The current HTTP request.
     * @param callable $next The next middleware or request handler.
     * @return Response The HTTP response.
     */
    public function handle(Request $request, callable $next): Response
    {
        $key = 'throttle_' . md5($request->ip() . $request->path());

        $attempts = (int)get_transient($key);

        if ($attempts >= $this->maxAttempts) {
            return ResponseFacade::error('Too many requests', 429);
        }

        set_transient($key, $attempts + 1, $this->decaySeconds);

        return $next($request);
    }

    /**
     * Set the maximum number of allowed attempts.
     *
     * @param int $attempts
     * @return static
     */
    public function maxAttempts(int $attempts): static
    {
        $this->maxAttempts = $attempts;
        return $this;
    }

    /**
     * Set the decay time in seconds.
     *
     * @param int $seconds
     * @return static
     */
    public function decaySeconds(int $seconds): static
    {
        $this->decaySeconds = $seconds;
        return $this;
    }
}
