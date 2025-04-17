<?php

declare(strict_types=1);

namespace WPJarvis\Core\Http;

use WPJarvis\Core\Support\Traits\Macroable;

/**
 * Class Response
 *
 * A Laravel-style HTTP response wrapper with JSON support,
 * WordPress REST response conversion, and fluent API.
 */
class Response
{
    use Macroable;

    /**
     * The content of the response.
     *
     * @var mixed
     */
    protected mixed $content;

    /**
     * HTTP status code.
     *
     * @var int
     */
    protected int $status = 200;

    /**
     * Headers to be sent with the response.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * Response constructor.
     *
     * @param mixed $content
     * @param int $status
     * @param array<string, string> $headers
     */
    public function __construct(mixed $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Create a response.
     *
     * @param mixed $content
     * @param int $status
     * @param array<string, string> $headers
     * @return static
     */
    public static function make(mixed $content = '', int $status = 200, array $headers = []): static
    {
        return new static($content, $status, $headers);
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array<string, string> $headers
     * @return static
     */
    public static function json(mixed $data, int $status = 200, array $headers = []): static
    {
        $headers['Content-Type'] = 'application/json';
        return new static(json_encode($data, JSON_UNESCAPED_UNICODE), $status, $headers);
    }

    /**
     * Return a success response (standardized structure).
     *
     * @param mixed $data
     * @param int $status
     * @param array<string, string> $headers
     * @return static
     */
    public static function success(mixed $data, int $status = 200, array $headers = []): static
    {
        return static::json([
            'success' => true,
            'data' => $data,
        ], $status, $headers);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $status
     * @param array<string, string> $headers
     * @return static
     */
    public static function error(string $message, int $status = 400, array $headers = []): static
    {
        return static::json([
            'success' => false,
            'error' => $message,
        ], $status, $headers);
    }

    /**
     * Set the response content.
     *
     * @param mixed $content
     * @return $this
     */
    public function setContent(mixed $content): static
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the response content.
     *
     * @return mixed
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * Set HTTP status code.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set a single header.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set multiple headers at once.
     *
     * @param array<string, string> $headers
     * @return $this
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Get all headers.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send headers and content to the browser.
     *
     * @return void
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        echo $this->content;
    }

    /**
     * Convert to WordPress-compatible WP_REST_Response object.
     *
     * @return \WP_REST_Response|mixed
     */
    public function toWordPressResponse(): mixed
    {
        if (class_exists(\WP_REST_Response::class)) {
            $response = new \WP_REST_Response($this->content, $this->status);

            foreach ($this->headers as $key => $value) {
                $response->header($key, $value);
            }

            return $response;
        }

        return $this->content;
    }

    /**
     * Redirect to a different URL.
     *
     * @param string $url
     * @param int $status
     * @return static
     */
    public static function redirect(string $url, int $status = 302): static
    {
        return new static('', $status, ['Location' => $url]);
    }

    /**
     * Output response as a downloadable file.
     *
     * @param string $filename
     * @param string|null $contentType
     * @return void
     */
    public function download(string $filename, ?string $contentType = null): void
    {
        if (!headers_sent()) {
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            header('Content-Length: ' . strlen($this->content));

            if ($contentType) {
                header("Content-Type: {$contentType}");
            } else {
                header('Content-Type: application/octet-stream');
            }

            foreach ($this->headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        echo $this->content;
        exit;
    }
}