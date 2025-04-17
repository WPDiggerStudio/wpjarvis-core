<?php

declare(strict_types=1);

namespace WPJarvis\Core\Console\Commands;

use WPJarvis\Core\Console\Command;

/**
 * Class GenerateController
 *
 * Command to scaffold new controller classes using various templates:
 * - Plain
 * - Resource
 * - API
 * - API Resource
 */
class GenerateController extends Command
{
    /**
     * Command signature.
     */
    protected string $signature = 'make:controller {name} [--resource] [--api] [--path=]';

    /**
     * Description displayed in WP-CLI help.
     */
    protected string $description = 'Generate a new controller class';

    /**
     * Execute the command logic.
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     */
    public function handle(array $args, array $assoc_args): void
    {
        $name = $args[0] ?? '';

        if (empty($name)) {
            $this->error('Controller name is required.');
            return;
        }

        $isResource = isset($assoc_args['resource']);
        $isApi = isset($assoc_args['api']);
        $basePath = $assoc_args['path'] ?? $this->getDefaultControllerPath();

        $filePath = $this->getFilePath($name, $basePath);

        if (file_exists($filePath)) {
            $this->error("Controller already exists: {$filePath}");
            return;
        }

        $this->makeDirectory(dirname($filePath));

        $stub = $this->getControllerStub($isResource, $isApi);
        $content = $this->replaceStubPlaceholders($stub, $name);

        file_put_contents($filePath, $content);

        $this->info("Controller created successfully: {$filePath}");
    }

    /**
     * Choose the appropriate controller stub.
     */
    protected function getControllerStub(bool $isResource, bool $isApi): string
    {
        return match (true) {
            $isResource && $isApi => $this->getApiResourceControllerStub(),
            $isResource => $this->getResourceControllerStub(),
            $isApi => $this->getApiControllerStub(),
            default => $this->getPlainControllerStub(),
        };
    }

    /**
     * Replace placeholders in stub with namespace and class.
     */
    protected function replaceStubPlaceholders(string $stub, string $name): string
    {
        return str_replace(
            ['{{namespace}}', '{{class}}'],
            [$this->getNamespace($name), $this->getClassName($name)],
            $stub
        );
    }

    /**
     * Get the full namespace based on name input.
     */
    protected function getNamespace(string $name): string
    {
        $parts = explode('/', $name);
        array_pop($parts);

        return 'App\\Http\\Controllers' . (!empty($parts) ? '\\' . implode('\\', $parts) : '');
    }

    /**
     * Get the class name.
     */
    protected function getClassName(string $name): string
    {
        $parts = explode('/', $name);
        $class = array_pop($parts);

        return str_ends_with($class, 'Controller') ? $class : $class . 'Controller';
    }

    /**
     * Generate the file path.
     */
    protected function getFilePath(string $name, string $basePath): string
    {
        return rtrim($basePath, '/') . '/' . str_replace('\\', '/', $this->getClassName($name)) . '.php';
    }

    /**
     * Get the default controller directory.
     */
    protected function getDefaultControllerPath(): string
    {
        return (defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : getcwd()) . '/app/Http/Controllers';
    }

    // ——— STUB TEMPLATES BELOW ———

    protected function getPlainControllerStub(): string
    { /* unchanged */
        return <<<'EOT'
// [plain stub content]
EOT;
    }

    protected function getApiControllerStub(): string
    { /* unchanged */
        return <<<'EOT'
// [api stub content]
EOT;
    }

    protected function getResourceControllerStub(): string
    { /* unchanged */
        return <<<'EOT'
// [resource stub content]
EOT;
    }

    protected function getApiResourceControllerStub(): string
    { /* unchanged */
        return <<<'EOT'
// [api resource stub content]
EOT;
    }
}
