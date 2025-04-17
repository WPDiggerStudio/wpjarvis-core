<?php

declare(strict_types=1);

namespace WPJarvis\Core\Exceptions;

use Exception;

/**
 * Class RouteNotFoundException
 *
 * Thrown when no route matches the request.
 */
class RouteNotFoundException extends Exception
{
    public function __construct(string $message = 'Route not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
