<?php


declare(strict_types=1);

namespace WPJarvis\Core\Exceptions;

use Illuminate\Contracts\Container\BindingResolutionException;
use WPJarvis\Core\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register the handler singleton.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('exception.handler', function () {
            return new Handler();
        });
    }

    /**
     * Bootstrap exception handling.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        set_exception_handler(function (\Throwable $e) {
            $this->app->make('exception.handler')->handle($e);
        });
    }
}
