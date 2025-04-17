<?php

declare(strict_types=1);

namespace WPJarvis\Core\Console;

use WPJarvis\Core\Container\Container;

/**
 * Abstract base command for WPJarvis CLI system.
 *
 * Supports Laravel-style command signatures, automatic WP-CLI registration,
 * stub generation, filesystem operations, helper methods, and scheduled execution.
 */
abstract class Command
{
    /**
     * Application container instance.
     */
    protected Container $app;

    /**
     * Command signature (e.g., "make:controller {name}").
     */
    protected string $signature;

    /**
     * Command description (used in WP-CLI help output).
     */
    protected string $description = '';

    /**
     * Create a new command instance.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Parse the signature into WP-CLI-compatible segments.
     *
     * @return array{name: string, arguments: array, options: array}
     */
    protected function parseSignature(): array
    {
        $parts = explode(' ', trim($this->signature));
        $name = array_shift($parts);

        $arguments = [];
        $options = [];

        foreach ($parts as $part) {
            if (str_starts_with($part, '--')) {
                $option = ltrim($part, '-');

                if (str_contains($option, '=')) {
                    [$optionName, $description] = array_pad(explode('=', $option, 2), 2, '');
                    $options[$optionName] = [
                        'type' => 'assoc',
                        'description' => trim($description, '[]'),
                        'optional' => str_starts_with($description, '['),
                    ];
                } else {
                    $options[$option] = [
                        'type' => 'flag',
                        'description' => '',
                        'optional' => true,
                    ];
                }
            } else {
                $arg = trim($part, '{}[]');
                $arguments[$arg] = [
                    'type' => 'positional',
                    'description' => '',
                    'optional' => str_starts_with($part, '['),
                ];
            }
        }

        return compact('name', 'arguments', 'options');
    }

    /**
     * Get the command name.
     */
    public function getName(): string
    {
        return $this->parseSignature()['name'];
    }

    /**
     * Get the description for WP-CLI output.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Register this command with WP-CLI.
     */
    public function register(): void
    {
        if (!class_exists('WP_CLI')) {
            return;
        }

        $parsed = $this->parseSignature();
        $this->registerWithWPCLI($parsed['name'], $parsed['arguments'], $parsed['options']);
    }

    /**
     * Register a command handler with WP-CLI.
     */
    protected function registerWithWPCLI(string $command, array $arguments, array $options): void
    {
        $instance = $this;

        WP_CLI::add_command("wpjarvis {$command}", function ($args, $assoc_args) use ($instance) {
            try {
                $instance->handle($args, $assoc_args);
            } catch (\Throwable $e) {
                WP_CLI::error($e->getMessage());
            }
        }, [
            'shortdesc' => $this->getDescription(),
            'synopsis' => $this->buildSynopsis($arguments, $options),
        ]);
    }

    /**
     * Build WP-CLI synopsis from parsed signature.
     */
    protected function buildSynopsis(array $arguments, array $options): array
    {
        $synopsis = [];

        foreach ($arguments as $name => $config) {
            $synopsis[] = [
                'type' => 'positional',
                'name' => $name,
                'description' => $config['description'] ?? '',
                'optional' => $config['optional'] ?? false,
            ];
        }

        foreach ($options as $name => $config) {
            $synopsis[] = [
                'type' => $config['type'],
                'name' => $name,
                'description' => $config['description'] ?? '',
                'optional' => $config['optional'] ?? true,
            ];
        }

        return $synopsis;
    }

    /**
     * Execute this command via scheduler (e.g., via WP-Cron).
     *
     * This method is intended for scheduled background jobs.
     * It internally calls `handle()` with no CLI arguments,
     * logs exceptions if thrown, and returns an exit code.
     *
     * @param array<string, mixed> $arguments
     * @return int Exit code (0 for success, 1 for failure)
     */
    public function handleViaScheduler(array $arguments = []): int
    {
        try {
            $this->handle($arguments, []);
            return 0;
        } catch (\Throwable $e) {
            if (class_exists('WP_CLI')) {
                WP_CLI::error("[Scheduled] " . $e->getMessage());
            } else {
                error_log('[WPJarvis Scheduled Error] ' . $e->getMessage());
            }
            return 1;
        }
    }

    /**
     * Required method for running the command logic.
     *
     * @param array $args Positional arguments
     * @param array $assoc_args Named options
     * @return void
     */
    abstract public function handle(array $args, array $assoc_args): void;

    /**
     * Get the contents of a stub file.
     *
     * @param string $stub
     * @return string
     */
    protected function getStub(string $stub): string
    {
        $stubPath = __DIR__ . "/stubs/{$stub}.stub";

        if (!file_exists($stubPath)) {
            throw new \RuntimeException("Stub file not found: {$stubPath}");
        }

        return (string)file_get_contents($stubPath);
    }

    /**
     * Create directory if it doesn't already exist.
     *
     * @param string $path
     * @return void
     */
    protected function makeDirectory(string $path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
    }

    /**
     * Output info message.
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        WP_CLI::success($message);
    }

    /**
     * Output warning message.
     *
     * @param string $message
     * @return void
     */
    protected function warn(string $message): void
    {
        WP_CLI::warning($message);
    }

    /**
     * Output error message.
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        WP_CLI::error($message);
    }

    /**
     * Prompt user for input.
     *
     * @param string $question
     * @return string
     */
    protected function ask(string $question): string
    {
        return WP_CLI::prompt($question);
    }

    /**
     * Ask for confirmation.
     *
     * @param string $message
     * @return bool
     */
    protected function confirm(string $message): bool
    {
        return WP_CLI::confirm($message, false);
    }
}
