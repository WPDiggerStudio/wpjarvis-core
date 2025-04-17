<?php

declare(strict_types=1);

namespace WPJarvis\Core\Console\Commands;

use WPJarvis\Core\Console\Command;

/**
 * Class GenerateModel
 *
 * Command to generate a new Eloquent-style model class,
 * with optional migration and controller generation.
 */
class GenerateModel extends Command
{
    /**
     * Signature used for WP-CLI registration.
     */
    protected string $signature = 'make:model {name} [--migration] [--controller] [--resource] [--path=]';

    /**
     * Description of the command.
     */
    protected string $description = 'Generate a new model class';

    /**
     * Handle the command execution.
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     */
    public function handle(array $args, array $assoc_args): void
    {
        $name = $args[0] ?? '';

        if (empty($name)) {
            $this->error('Model name is required.');
            return;
        }

        $createMigration = isset($assoc_args['migration']);
        $createController = isset($assoc_args['controller']);
        $isResource = isset($assoc_args['resource']);
        $basePath = $assoc_args['path'] ?? $this->getDefaultModelPath();

        $filePath = $this->createModel($name, $basePath);

        if ($createMigration) {
            $this->createMigration($name);
        }

        if ($createController) {
            $this->createController($name, $isResource);
        }

        $this->info("Model created successfully: {$filePath}");
    }

    /**
     * Generate the model file from a stub.
     */
    protected function createModel(string $name, string $basePath): string
    {
        $filePath = $this->getModelFilePath($name, $basePath);

        if (file_exists($filePath)) {
            $this->error("Model already exists: {$filePath}");
            return $filePath;
        }

        $this->makeDirectory(dirname($filePath));

        $stub = $this->getModelStub();
        $content = $this->replaceModelStubPlaceholders($stub, $name);

        file_put_contents($filePath, $content);

        return $filePath;
    }

    /**
     * Generate a migration for the model.
     */
    protected function createMigration(string $name): void
    {
        if (!class_exists('WP_CLI')) {
            return;
        }

        $tableName = $this->getTableName($name);
        $migrationName = 'create_' . $tableName . '_table';

        WP_CLI::runcommand("wpjarvis make:migration {$migrationName} --create={$tableName}", [
            'launch' => false,
            'exit_error' => false,
        ]);
    }

    /**
     * Generate a controller for the model.
     */
    protected function createController(string $name, bool $isResource): void
    {
        if (!class_exists('WP_CLI')) {
            return;
        }

        $controllerName = $name . 'Controller';
        $resourceFlag = $isResource ? '--resource' : '';

        WP_CLI::runcommand("wpjarvis make:controller {$controllerName} {$resourceFlag}", [
            'launch' => false,
            'exit_error' => false,
        ]);
    }

    /**
     * Get the model stub template.
     */
    protected function getModelStub(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

namespace {{namespace}};

use WPJarvis\Core\Database\Model;

class {{class}} extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = '{{table}}';

    /**
     * The primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Enable automatic timestamps.
     *
     * @var bool
     */
    protected $timestamps = true;

    /**
     * Mass assignable attributes.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Attributes casting.
     *
     * @var array
     */
    protected $casts = [];
}
EOT;
    }

    /**
     * Replace placeholders in the model stub.
     */
    protected function replaceModelStubPlaceholders(string $stub, string $name): string
    {
        $namespace = $this->getNamespace($name);
        $className = $this->getClassName($name);
        $table = $this->getTableName($className);

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{table}}'],
            [$namespace, $className, $table],
            $stub
        );
    }

    /**
     * Derive the full namespace from input.
     */
    protected function getNamespace(string $name): string
    {
        $parts = explode('/', $name);
        array_pop($parts);

        return 'App\\Models' . (!empty($parts) ? '\\' . implode('\\', $parts) : '');
    }

    /**
     * Extract the class name from input.
     */
    protected function getClassName(string $name): string
    {
        $parts = explode('/', $name);
        return array_pop($parts);
    }

    /**
     * Resolve the model file path.
     */
    protected function getModelFilePath(string $name, string $basePath): string
    {
        return rtrim($basePath, '/') . '/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Get the default base path for models.
     */
    protected function getDefaultModelPath(): string
    {
        return (defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : getcwd()) . '/app/Models';
    }

    /**
     * Convert model name to a snake_case plural table name.
     */
    protected function getTableName(string $modelName): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName)) . 's';
    }
}