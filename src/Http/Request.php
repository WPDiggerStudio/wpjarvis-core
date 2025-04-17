<?php

declare(strict_types=1);

namespace WPJarvis\Core\Http;

use Closure;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use WPJarvis\Core\Support\Traits\Macroable;

/**
 * Class Request
 *
 * A Laravel-style HTTP request object customized for WordPress and Symfony environments.
 */
class Request
{
    use Macroable;

    /**
     * The Symfony request instance.
     *
     * @var SymfonyRequest
     */
    protected SymfonyRequest $request;

    /**
     * The WordPress REST request instance.
     *
     * @var mixed|null
     */
    protected mixed $wpRestRequest = null;

    /**
     * Temporary store for old input (for flashing).
     *
     * @var array<string, mixed>
     */
    protected array $oldInput = [];

    /**
     * Flash storage.
     *
     * @var array<string, mixed>
     */
    protected array $flashStorage = [];

    /**
     * Request constructor.
     *
     * @param mixed|null $wpRestRequest Optional WordPress REST request object
     */
    public function __construct(mixed $wpRestRequest = null)
    {
        $this->request = SymfonyRequest::createFromGlobals();
        $this->wpRestRequest = $wpRestRequest;
    }

    /**
     * Get all input from GET, POST, and FILES.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->wpRestRequest) {
            return $this->wpRestRequest->get_params();
        }

        return array_merge(
            $this->request->query->all(),
            $this->request->request->all(),
            $this->request->files->all()
        );
    }

    /**
     * Get an input value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->wpRestRequest) {
            return $this->wpRestRequest->get_param($key) ?? $default;
        }

        return $this->request->get($key, $default);
    }

    /**
     * Check if an input exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if ($this->wpRestRequest) {
            return $this->wpRestRequest->has_param($key);
        }

        return $this->request->query->has($key)
            || $this->request->request->has($key)
            || $this->request->files->has($key);
    }

    /**
     * Get only specified keys from input.
     *
     * @param array|string $keys
     * @return array<string, mixed>
     */
    public function only(array|string $keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_intersect_key($this->all(), array_flip($keys));
    }

    /**
     * Get all input except specified keys.
     *
     * @param array|string $keys
     * @return array<string, mixed>
     */
    public function except(array|string $keys): array
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return array_diff_key($this->all(), array_flip($keys));
    }

    /**
     * Get a sanitized input value (text-based).
     *
     * @param string $key
     * @param mixed $default
     * @return string|null
     */
    public function sanitize(string $key, mixed $default = null): ?string
    {
        $value = $this->input($key, $default);
        return is_string($value) ? sanitize_text_field($value) : null;
    }

    /**
     * Sanitize an array of input values by their keys using `sanitize_text_field`.
     *
     * @param array $keys An array of input keys to sanitize.
     * @return array An associative array of sanitized input values.
     */
    public function sanitizeArray(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = sanitize_text_field($this->input($key));
        }
        return $data;
    }

    /**
     * Apply a custom filter on an input.
     *
     * @param string $key
     * @param Closure $filter
     * @param mixed $default
     * @return mixed
     */
    public function filterInput(string $key, Closure $filter, mixed $default = null): mixed
    {
        $value = $this->input($key, $default);
        return $filter($value);
    }

    /**
     * Get a cookie value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->request->cookies->get($key, $default);
    }

    /**
     * Get a file from the request.
     *
     * @param string $key
     * @return mixed
     */
    public function file(string $key): mixed
    {
        return $this->request->files->get($key);
    }

    /**
     * Get a header value.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function header(string $key, ?string $default = null): ?string
    {
        return $this->request->headers->get($key, $default);
    }

    /**
     * Determine if the request is an Ajax call.
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return strtolower($this->header('X-Requested-With') ?? '') === 'xmlhttprequest';
    }

    /**
     * Determine if the request expects a JSON response.
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        return str_contains((string)$this->header('Accept'), 'application/json');
    }

    /**
     * Get the request method (GET, POST, etc).
     *
     * @return string
     */
    public function method(): string
    {
        return $this->request->getMethod();
    }

    /**
     * Check if the request method matches.
     *
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->method();
    }

    /**
     * Get the URI path (e.g., /blog/post).
     *
     * @return string
     */
    public function path(): string
    {
        return $this->request->getPathInfo();
    }

    /**
     * Get the full URL (including query).
     *
     * @return string
     */
    public function fullUrl(): string
    {
        return $this->request->getUri();
    }

    /**
     * Get just the base URL (no query string).
     *
     * @return string
     */
    public function url(): string
    {
        return $this->request->getSchemeAndHttpHost() . $this->request->getPathInfo();
    }

    /**
     * Get the IP address of the client.
     *
     * @return string
     */
    public function ip(): string
    {
        return $this->request->getClientIp() ?? '0.0.0.0';
    }

    /**
     * Flash an input to temporary storage.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function flash(string $key, mixed $value): void
    {
        $this->flashStorage[$key] = $value;
    }

    /**
     * Retrieve flashed data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function old(string $key, mixed $default = null): mixed
    {
        return $this->flashStorage[$key] ?? $default;
    }

    /**
     * Get the original Symfony request.
     *
     * @return SymfonyRequest
     */
    public function getSymfonyRequest(): SymfonyRequest
    {
        return $this->request;
    }

    /**
     * Get the WordPress REST request.
     *
     * @return mixed|null
     */
    public function getWpRestRequest(): mixed
    {
        return $this->wpRestRequest;
    }
}