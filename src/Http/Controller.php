<?php

declare(strict_types=1);

namespace WPJarvis\Core\Http;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Foundation\Application;
use WPJarvis\Core\Support\Traits\Macroable;

/**
 * Class Controller
 *
 * Base controller class for all WPJarvis HTTP controllers.
 * Provides convenient helpers for responses, views, and dependency injection.
 */
abstract class Controller
{
    use Macroable;

    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Middleware assigned to the controller.
     *
     * @var array<int, array{middleware: string, options: array}>
     */
    protected array $middleware = [];

    /**
     * Controller constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->registerMiddleware();
    }

    /**
     * Register the middleware assigned to this controller.
     *
     * @return void
     */
    protected function registerMiddleware(): void
    {
        // This assumes your router or kernel handles these declarations
        foreach ($this->middleware as $definition) {
            // Do nothing here for now – router should register these
            // You can extend this method later for global middleware registration
        }
    }

    /**
     * Assign middleware to the controller.
     *
     * @param string|array $middleware
     * @param array $options
     * @return void
     */
    protected function middleware(string|array $middleware, array $options = []): void
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];

        foreach ($middlewares as $m) {
            $this->middleware[] = [
                'middleware' => $m,
                'options' => $options,
            ];
        }
    }

    /**
     * Execute the specified method on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callAction(string $method, array $parameters): mixed
    {
        return $this->app->call([$this, $method], $parameters);
    }

    /**
     * Create a new JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function json(mixed $data, int $status = 200, array $headers = []): Response
    {
        return Response::json($data, $status, $headers);
    }

    /**
     * Return a plain content response.
     *
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function response(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function error(string $message, int $status = 400, array $headers = []): Response
    {
        return Response::error($message, $status, $headers);
    }

    /**
     * Return a success response.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function success(mixed $data, int $status = 200, array $headers = []): Response
    {
        return Response::success($data, $status, $headers);
    }

    /**
     * Render a Blade view.
     *
     * @param string $view
     * @param array $data
     * @return string
     * @throws BindingResolutionException
     */
    public function view(string $view, array $data = []): string
    {
        return $this->app->make('view')->render($view, $data);
    }

    /**
     * Redirect to a given URL.
     *
     * @param string $url
     * @param int $status
     * @return Response
     */
    public function redirect(string $url, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $url]);
    }

    /**
     * Access the current request instance.
     *
     * @return Request
     * @throws BindingResolutionException
     */
    public function request(): Request
    {
        return $this->app->make(Request::class);
    }
}