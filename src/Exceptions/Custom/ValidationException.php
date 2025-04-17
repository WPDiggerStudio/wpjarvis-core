<?php

declare(strict_types=1);

namespace WPJarvis\Core\Exceptions;

use Exception;

/**
 * Class ValidationException
 *
 * Thrown when validation fails.
 */
class ValidationException extends Exception
{
    protected array $errors;

    public function __construct(array $errors, string $message = 'Validation failed', int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
