<?php
declare(strict_types=1);

namespace WPJarvis\Core\Http\Middleware;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Support\ServiceProvider;

/**
 * Registers middleware aliases and groups into the config.
 */
class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register middleware aliases and groups.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register(): void
    {
        $this->app->make('config')->set('middleware', [
            'aliases' => [
                'csrf' => VerifyCsrfToken::class,
                // Add more aliases here
            ],
            'groups' => [
                'web' => [
                    'csrf',
                    // Add more middleware for the "web" group
                ],
                'api' => [
                    // Define middleware stack for APIs
                ],
            ],
        ]);
    }

    /**
     * Register WordPress hooks if needed.
     *
     * @return void
     */
    public function registerHooks(): void
    {
        // No hooks by default
    }
}
