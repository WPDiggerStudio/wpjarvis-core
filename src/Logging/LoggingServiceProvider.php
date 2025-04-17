<?php

declare(strict_types=1);

namespace WPJarvis\Core\Logging;

use Illuminate\Contracts\Container\BindingResolutionException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;
use WPJarvis\Core\Support\ServiceProvider;
use Illuminate\Log\LogManager;

/**
 * LoggingServiceProvider
 *
 * Registers and configures the Monolog-based logger service.
 *
 * @package WPJarvis\Core\Logging
 */
class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register the logging service in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('log', function ($app) {
            $config = $app->make('config')->get('logging', []);

            // Setup log channel or default to single file logger
            $logger = new Logger($config['channel'] ?? 'wpjarvis');

            $logPath = $config['path'] ?? $app->basePath('storage/logs/wpjarvis.log');
            $level = $this->parseLogLevel($config['level'] ?? 'debug');

            // Handler setup
            $handler = new StreamHandler($logPath, $level);
            $handler->setFormatter(new LineFormatter(null, null, true, true));

            $logger->pushHandler($handler);

            // Optional: also log to PHP error log
            if (!empty($config['use_error_log'])) {
                $logger->pushHandler(new ErrorLogHandler());
            }

            return $logger;
        });

        $this->app->alias('log', LoggerInterface::class);
    }

    /**
     * Parse the log level from config string.
     *
     * @param string $level
     * @return int
     */
    protected function parseLogLevel(string $level): int
    {
        return Logger::toMonologLevel($level);
    }

    /**
     * Register WordPress hooks, if needed for logging integration.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function registerHooks(): void
    {
        // Example: log plugin initialization
        add_action('plugins_loaded', function () {
            $this->app->make('log')->info('WPJarvis logging initialized.');
        });
    }
}
