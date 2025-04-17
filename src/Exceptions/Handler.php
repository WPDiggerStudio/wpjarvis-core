<?php

declare(strict_types=1);

namespace WPJarvis\Core\Exceptions;

use Throwable;
use WPJarvis\Core\Support\Facades\Log;
use WPJarvis\Core\Http\Response;
use WPJarvis\Core\Http\Request;

/**
 * Class Handler
 *
 * Centralized exception handling for the WPJarvis framework.
 * Inspired by Laravel, adapted for WordPress error handling.
 */
class Handler
{
    /**
     * Report or log an exception.
     *
     * @param Throwable $e
     * @return void
     */
    public function report(Throwable $e): void
    {
        // Send to logger if available
        if (class_exists(Log::class)) {
            Log::error($e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
        } else {
            error_log($e->__toString());
        }
    }

    /**
     * Render an exception into an HTTP or WordPress-friendly response.
     *
     * @param Request|null $request
     * @param Throwable $e
     * @return mixed
     */
    public function render(?Request $request, Throwable $e): mixed
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json_error(['error' => $e->getMessage()], 500);
        }

        if (defined('REST_REQUEST') && REST_REQUEST) {
            return new \WP_Error('wpjarvis_exception', $e->getMessage(), ['status' => 500]);
        }

        return Response::error('Internal Server Error', 500);
    }

    /**
     * Handle the uncaught exception.
     *
     * @param Throwable $e
     * @return void
     */
    public function handle(Throwable $e): void
    {
        $this->report($e);

        if (php_sapi_name() === 'cli') {
            echo '[Exception] ' . $e->getMessage() . PHP_EOL;
            exit(1);
        }

        $this->render(null, $e)->send();
    }
}