<?php

declare(strict_types=1);

namespace WPJarvis\Core\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

/**
 * Service Container
 *
 * A lightweight dependency injection container inspired by Laravel.
 */
class Container
{
    /**
     * The globally available container instance.
     *
     * @var static|null
     */
    protected static ?self $instance = null;

    /**
     * The container's bindings.
     *
     * @var array<string, array{concrete: Closure, shared: bool}>
     */
    protected array $bindings = [];

    /**
     * The container's shared instances.
     *
     * @var array<string, mixed>
     */
    protected array $instances = [];

    /**
     * The registered type aliases.
     *
     * @var array<string, string>
     */
    protected array $aliases = [];

    /**
     * The registered service providers.
     *
     * @var array<int, \WPJarvis\Core\Support\ServiceProvider>
     */
    protected array $serviceProviders = [];

    /**
     * Indicates if the application has been booted.
     *
     * @var bool
     */
    protected bool $booted = false;

    /**
     * Get the globally available container instance.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Set the globally available container instance.
     *
     * @param static $container
     * @return void
     */
    public static function setInstance(self $container): void
    {
        static::$instance = $container;
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        unset($this->instances[$abstract]);

        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, (string)$concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Register a shared binding in the container.
     *
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return mixed
     */
    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param string $abstract
     * @param string $concrete
     * @return Closure
     */
    protected function getClosure(string $abstract, string $concrete): Closure
    {
        return function (self $container, array $parameters = []) use ($abstract, $concrete): mixed {
            if ($abstract === $concrete) {
                return $container->build($concrete, $parameters);
            }

            return $container->make($concrete, $parameters);
        };
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     *
     * @throws Exception
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        $object = $this->isBuildable($concrete, $abstract)
            ? $this->build($concrete, $parameters)
            : $this->make($concrete, $parameters);

        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param string $abstract
     * @return mixed
     */
    protected function getConcrete(string $abstract): mixed
    {
        return $this->bindings[$abstract]['concrete'] ?? $abstract;
    }

    /**
     * Get the alias for an abstract if available.
     *
     * @param string $abstract
     * @return string
     */
    protected function getAlias(string $abstract): string
    {
        return $this->aliases[$abstract] ?? $abstract;
    }

    /**
     * Determine if the given concrete is buildable.
     *
     * @param mixed $concrete
     * @param string $abstract
     * @return bool
     */
    protected function isBuildable(mixed $concrete, string $abstract): bool
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Determine if a given type is shared.
     *
     * @param string $abstract
     * @return bool
     */
    protected function isShared(string $abstract): bool
    {
        return $this->bindings[$abstract]['shared'] ?? false;
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param Closure|string $concrete
     * @param array $parameters
     * @return mixed
     *
     * @throws Exception
     */
    public function build(Closure|string $concrete, array $parameters = []): mixed
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        try {
            $reflector = new ReflectionClass($concrete);
        } catch (ReflectionException $e) {
            throw new Exception("Target class [$concrete] does not exist.", 0, $e);
        }

        if (!$reflector->isInstantiable()) {
            throw new Exception("Target [$concrete] is not instantiable.");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param array<int, ReflectionParameter> $dependencies
     * @param array $parameters
     * @return array
     *
     * @throws Exception
     */
    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $paramName = $dependency->getName();

            if (array_key_exists($paramName, $parameters)) {
                $results[] = $parameters[$paramName];
                continue;
            }

            $results[] = $this->resolveClass($dependency);
        }

        return $results;
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param ReflectionParameter $parameter
     * @return mixed
     *
     * @throws Exception
     */
    protected function resolveClass(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (!$type || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception("Unresolvable dependency resolving [\${$parameter->getName()}] in class {$parameter->getDeclaringClass()?->getName()}");
        }

        try {
            return $this->make($type->getName());
        } catch (Exception $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     * Register a service provider with the application.
     *
     * @param ServiceProvider|string $provider
     * @return ServiceProvider
     */
    public function register(ServiceProvider|string $provider): ServiceProvider
    {
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();
        $this->serviceProviders[] = $provider;

        if ($this->booted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     * @return ServiceProvider
     */
    protected function resolveProvider(string $provider): ServiceProvider
    {
        return new $provider($this);
    }

    /**
     * Boot the given service provider.
     *
     * @param ServiceProvider $provider
     * @return void
     */
    protected function bootProvider(ServiceProvider $provider): void
    {
        $provider->boot();
    }

    /**
     * Boot all service providers.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->serviceProviders as $provider) {
            $this->bootProvider($provider);
        }

        $this->booted = true;
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * Check if the container has a binding.
     *
     * @param string $abstract
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]) || isset($this->aliases[$abstract]);
    }

    /**
     * Get a registered service provider instance.
     *
     * @param string $providerClass
     * @return ServiceProvider|null
     */
    public function getProvider(string $providerClass): ?ServiceProvider
    {
        foreach ($this->serviceProviders as $provider) {
            if ($provider instanceof $providerClass) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * Get registered service providers.
     *
     * @return array<int, ServiceProvider>
     */
    public function getProviders(): array
    {
        return $this->serviceProviders;
    }

    /**
     * Alias a type to a different name.
     *
     * @param string $abstract
     * @param string $alias
     * @return void
     */
    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param callable|string $callback
     * @param array $parameters
     * @return mixed
     *
     * @throws Exception
     */
    public function call(callable|string $callback, array $parameters = []): mixed
    {
        if (is_string($callback) && strpos($callback, '@') !== false) {
            [$class, $method] = explode('@', $callback);
            $callback = [$this->make($class), $method];
        }

        try {
            $reflection = is_array($callback)
                ? new \ReflectionMethod($callback[0], $callback[1])
                : new \ReflectionFunction($callback);

            $dependencies = $reflection->getParameters();
            $resolved = $this->resolveDependencies($dependencies, $parameters);

            return call_user_func_array($callback, $resolved);
        } catch (\ReflectionException $e) {
            throw new Exception("Cannot call the provided callback.", 0, $e);
        }
    }
}