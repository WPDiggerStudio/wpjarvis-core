<?php

declare(strict_types=1);

namespace WPJarvis\Core\Foundation;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Support\ServiceProvider;
use WPJarvis\Core\Foundation\Bootstrap\LoadConfiguration;
use WPJarvis\Core\Foundation\Bootstrap\RegisterProviders;
use WPJarvis\Core\Foundation\Bootstrap\BootProviders;

/**
 * The WPJarvis Application class.
 *
 * This is the main entry point of the framework that bootstraps
 * all components and provides the service container.
 */
class Application extends Container
{
    /**
     * The base path of the plugin.
     */
    protected string $basePath;

    /**
     * The main plugin file path.
     */
    protected string $pluginFile;

    /**
     * All registered service providers.
     *
     * @var array<int, \Illuminate\Support\ServiceProvider>
     */
    protected array $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array<string, bool>
     */
    protected array $loadedProviders = [];

    /**
     * The bootstrap classes that will be run.
     *
     * @var array<int, class-string>
     */
    protected array $bootstrappers = [
        LoadConfiguration::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * Create a new WPJarvis application instance.
     *
     * @param string $basePath
     * @param string $pluginFile
     * @throws BindingResolutionException
     */
    public function __construct(string $basePath, string $pluginFile)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $this->pluginFile = $pluginFile;

        $this->registerBaseBindings();
        $this->bootstrap();
    }

    /**
     * Register the base container bindings.
     *
     * @return void
     */
    protected function registerBaseBindings(): void
    {
        static::setInstance($this);

        $this->instance('app', $this);
        $this->instance(self::class, $this);
        $this->instance(ContainerContract::class, $this);
    }

    /**
     * Bootstrap the application with all defined bootstrappers.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function bootstrap(): void
    {
        foreach ($this->bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }

        // Register global exception handler
        set_exception_handler(function ($e) {
            $this->make(\WPJarvis\Core\Exceptions\Handler::class)->handle($e);
        });
    }

    /**
     * Register a service provider.
     *
     * @param string|ServiceProvider $provider
     * @return ServiceProvider
     */
    public function register(ServiceProvider|string $provider): ServiceProvider
    {
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = true;

        return $provider;
    }

    /**
     * Resolve a service provider by class name.
     *
     * @param string $provider
     * @return ServiceProvider
     */
    protected function resolveProvider(string $provider): ServiceProvider
    {
        return new $provider($this);
    }

    /**
     * Boot all registered service providers.
     *
     * @return void
     */
    public function boot(): void
    {
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $this->call([$provider, 'boot']);
            }
        }
    }

    /**
     * Get the base path of the plugin.
     *
     * @param string $path
     * @return string
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the main plugin file.
     *
     * @return string
     */
    public function pluginFile(): string
    {
        return $this->pluginFile;
    }

    /**
     * Get the plugin directory name.
     *
     * @return string
     */
    public function pluginDirName(): string
    {
        return basename(dirname($this->pluginFile));
    }

    /**
     * Get the plugin version from config.
     *
     * @return string
     */
    public function version(): string
    {
        return $this['config']->get('app.version', 'dev');
    }

    /**
     * Get the plugin name from config.
     *
     * @return string
     */
    public function name(): string
    {
        return $this['config']->get('app.name', 'WPJarvis');
    }

    /**
     * Get the plugin text domain from config.
     *
     * @return string
     */
    public function textDomain(): string
    {
        return $this['config']->get('app.text_domain', 'wpjarvis');
    }

    /**
     * Get the application environment.
     *
     * @return string
     */
    public function environment(): string
    {
        return $this['config']->get('app.env', 'production');
    }

    /**
     * Check if the application is running in the console (e.g., WP-CLI).
     *
     * @return bool
     */
    public function runningInConsole(): bool
    {
        return defined('WP_CLI') && WP_CLI;
    }

    /**
     * Determine if the application is running in the given environment.
     *
     * @param string|array $environments
     * @return bool
     */
    public function isEnvironment(string|array $environments): bool
    {
        $env = $this->environment();

        return is_array($environments)
            ? in_array($env, $environments, true)
            : $env === $environments;
    }

    /**
     * Determine if the application is in production.
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->isEnvironment('production');
    }
}