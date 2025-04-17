<?php


declare(strict_types=1);

namespace WPJarvis\Core\Logging;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use WPJarvis\Core\Foundation\Application;

/**
 * Class Logger
 *
 * A centralized logging manager for WPJarvis using Monolog.
 */
class Logger implements LoggerInterface
{
    /**
     * The Monolog logger instance.
     */
    protected MonologLogger $logger;

    /**
     * Create a new logger instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $config = $app->make('config')->get('logging');

        $channel = $config['channel'] ?? 'wpjarvis';
        $level = $this->parseLevel($config['level'] ?? 'debug');
        $logPath = $config['path'] ?? $app->basePath('storage/logs/wpjarvis.log');

        $handler = new StreamHandler($logPath, $level);

        // Format output
        $handler->setFormatter(new LineFormatter(null, null, true, true));

        $this->logger = new MonologLogger($channel);
        $this->logger->pushHandler($handler);
    }

    /**
     * Get the underlying Monolog logger instance.
     *
     * @return MonologLogger
     */
    public function getMonolog(): MonologLogger
    {
        return $this->logger;
    }

    /**
     * Parse string log level into Monolog constant.
     *
     * @param string $level
     * @return int
     */
    protected function parseLevel(string $level): int
    {
        $level = strtolower($level);

        return match ($level) {
            'debug' => MonologLogger::DEBUG,
            'info' => MonologLogger::INFO,
            'notice' => MonologLogger::NOTICE,
            'warning' => MonologLogger::WARNING,
            'error' => MonologLogger::ERROR,
            'critical' => MonologLogger::CRITICAL,
            'alert' => MonologLogger::ALERT,
            'emergency' => MonologLogger::EMERGENCY,
            default => throw new InvalidArgumentException("Invalid log level: {$level}")
        };
    }

    // ------------------------------------------------------------------
    // PSR-3 LoggerInterface Methods
    // ------------------------------------------------------------------

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exceptions.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action.
     *
     * Example: Invalid user input, failed API call.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param array<string, mixed> $context
     * @return void
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
