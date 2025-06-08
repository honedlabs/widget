<?php

namespace Honed\Widget;

use Honed\Widget\Contracts\SerializesScope;
use Honed\Widget\Drivers\Decorator;
use Honed\Widget\Drivers\ArrayDriver;
use Honed\Widget\Drivers\CacheDriver;
use Honed\Widget\Drivers\CookieDriver;
use Honed\Widget\Drivers\DatabaseDriver;
use Honed\Widget\Exceptions\CannotSerializeScopeException;
use Illuminate\Contracts\Container\Container;
use Honed\Widget\Exceptions\InvalidDriverException;
use Honed\Widget\Exceptions\UndefinedDriverException;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;

/**
 * @mixin \Honed\Widget\Drivers\Decorator
 */
class WidgetManager
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The array of resolved Widget drivers.
     * 
     * @var array<string, \Honed\Widget\Drivers\Decorator>
     */
    protected $drivers = [];

    /**
     * The registered custom drivers.
     *
     * @var array<string, \Closure(\Illuminate\Contracts\Container\Container, array<string, mixed>):mixed>
     */
    protected $customDrivers = [];

    /**
     * The default scope resolver.
     * 
     * @var (callable(string):mixed)|null
     */
    protected $defaultScopeResolver;

    /**
     * Whether the Eloquent "morph map" should be used when serializing
     * the widget.
     * 
     * @var bool
     */
    protected $useMorphMap = false;

    /**
     * Create a new Widget manager instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get a Widget driver instance by name.
     * 
     * @param string|null $name
     * @return \Honed\Widget\Drivers\Decorator
     * 
     * @throws \Honed\Widget\Exceptions\UndefinedDriverException
     * @throws \Honed\Widget\Exceptions\InvalidDriverException
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] = $this->get($name);
    }

    /**
     * Attempt to get the driver from the local cache.
     *
     * @param  string  $name
     * @return \Honed\Widget\Drivers\Decorator
     * 
     * @throws \Honed\Widget\Exceptions\UndefinedDriverException
     * @throws \Honed\Widget\Exceptions\InvalidDriverException
     */
    public function get($name)
    {
        return $this->drivers[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given driver.
     *
     * @param  string  $name
     * @return \Honed\Widget\Drivers\Decorator
     *
     * @throws \Honed\Widget\Exceptions\UndefinedDriverException
     * @throws \Honed\Widget\Exceptions\InvalidDriverException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            UndefinedDriverException::throw($name);
        }

        /** @var string */
        $driver = $config['driver'];

        if (isset($this->customDrivers[$driver])) {
            $driver = $this->callCustomDriver($config);
        } else {
            $driverMethod = 'create'.ucfirst($driver).'Driver';

            if (method_exists($this, $driverMethod)) {
                /** @var \Honed\Widget\Contracts\Driver */
                $driver = $this->{$driverMethod}($config, $name);
            } else {
                InvalidDriverException::throw($driver);
            }
        }

        return new Decorator(
            $name,
            $driver,
            $this->defaultScopeResolver($name),
            $this->container,
        );
    }

    /**
     * Call a custom driver.
     *
     * @param array<string, mixed> $config
     * @return \Honed\Widget\Contracts\Driver
     */
    protected function callCustomDriver($config)
    {
        /** @var string */
        $driver = $config['driver'];

        /** @var \Honed\Widget\Contracts\Driver */
        return $this->customDrivers[$driver]($this->container, $config);
    }

    /**
     * Create an instance of the array driver.
     *
     * @return \Honed\Widget\Drivers\ArrayDriver
     */
    public function createArrayDriver()
    {
        /** @var \Illuminate\Contracts\Events\Dispatcher */
        $events = $this->container->get('events');

        return new ArrayDriver($events);
    }

    /**
     * Create an instance of the cache driver.
     * 
     * @return \Honed\Widget\Drivers\CacheDriver
     */
    public function createCacheDriver()
    {
        /** @var \Illuminate\Cache\CacheManager */
        $cache = $this->container->get('cache');

        /** @var \Illuminate\Contracts\Events\Dispatcher */
        $events = $this->container->get('events');

        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        return new CacheDriver($cache, $events, $config);
    }
    
    /**
     * Create an instance of the cookie driver.
     * 
     * @return \Honed\Widget\Drivers\CookieDriver
     */
    public function createCookieDriver()
    {
        /** @var \Illuminate\Cookie\CookieJar */
        $cookies = $this->container->get('cookie');

        /** @var \Illuminate\Contracts\Events\Dispatcher */
        $events = $this->container->get('events');

        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        return new CookieDriver($cookies, $events, $config);
    }

    /**
     * Create an instance of the database driver.
     *
     * @param array<string, mixed> $config
     * @param string $name
     * @return \Honed\Widget\Drivers\DatabaseDriver
     */
    public function createDatabaseDriver($config, $name)
    {
        /** @var \Illuminate\Database\DatabaseManager */
        $db = $this->container->get('db');

        /** @var \Illuminate\Contracts\Events\Dispatcher */
        $events = $this->container->get('events');

        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        return new DatabaseDriver(
            $db,
            $events,
            $config,
            $name
        );
    }

    /**
     * Specify that the Eloquent morph map should be used when serializing.
     *
     * @param  bool  $value
     * @return $this
     */
    public function useMorphMap($value = true)
    {
        $this->useMorphMap = $value;

        return $this;
    }

    /**
     * Serialize the given scope for storage.
     *
     * @param  mixed  $scope
     * @return string
     * 
     * @throws \Honed\Widget\Exceptions\CannotSerializeScopeException
     */
    public function serializeScope($scope)
    {
        return match (true) {
            $scope instanceof SerializesScope => $scope->serializeScope(),
            is_string($scope) => $scope,
            is_numeric($scope) => (string) $scope,
            $scope instanceof Model && $this->useMorphMap => $scope->getMorphClass().'|'.$scope->getKey(), // @phpstan-ignore binaryOp.invalid
            $scope instanceof Model && ! $this->useMorphMap => $scope::class.'|'.$scope->getKey(), // @phpstan-ignore binaryOp.invalid
            default => CannotSerializeScopeException::throw()
        };
    }

    /**
     * The default scope resolver.
     *
     * @param  string  $driver
     * @return callable(): mixed
     */
    protected function defaultScopeResolver($driver)
    {
        return function () use ($driver) {
            if (isset($this->defaultScopeResolver)) {
                return call_user_func($this->defaultScopeResolver, $driver);
            }

            /** @var \Illuminate\Contracts\Auth\Factory */
            $auth = $this->container->get('auth');

            return $auth->guard()->user();
        };
    }

    /**
     * Set the default scope resolver.
     *
     * @param  (callable(string): mixed)  $resolver
     * @return void
     */
    public function resolveScopeUsing($resolver)
    {
        $this->defaultScopeResolver = $resolver;
    }

    /**
     * Get the driver configuration.
     *
     * @param string $name
     * @return array<string, mixed>|null
     */
    public function getConfig($name)
    {
        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        /** @var array<string, mixed> */
        return $config->get("widget.drivers.{$name}");
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        /** @var string */
        return $config->get('widget.default') ?? 'database';
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        $config->set('widget.default', $name);
    }

    /**
     * Unset the given driver instances.
     *
     * @param  string|array<int, string>|null  $name
     * @return $this
     */
    public function forgetDriver($name = null)
    {
        $name ??= $this->getDefaultDriver();

        foreach ((array) $name as $driverName) {
            if (isset($this->drivers[$driverName])) {
                unset($this->drivers[$driverName]);
            }
        }

        return $this;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure(\Illuminate\Contracts\Container\Container, array<string, mixed>):mixed  $callback
     * @return $this
     */
    public function extend($driver, $callback)
    {
        $this->customDrivers[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        foreach ($this->drivers as $driver) {
            $driver->setContainer($container);
        }

        return $this;
    }

    /**
     * Get the widgets for an inertia page.
     * 
     * @param string|null $scope
     * @param string|null $group
     * @return mixed
     */
    public function inertia($scope = null, $group = null)
    {
        $callback = fn () => $this->driver()->get($scope, $group);

        return match ($this->getInertia()) {
            'defer' => Inertia::defer($callback, 'widgets'),
            'lazy' => Inertia::lazy($callback),
            default => $callback(),
        };
    }

    /**
     * Get the inertia retrieval method.
     * 
     * @return string
     */
    protected function getInertia()
    {
        /** @var \Illuminate\Contracts\Config\Repository */
        $config = $this->container->get('config');

        /** @var string */
        return $config->get('widget.inertia') ?? 'sync';
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array<array-key, mixed>  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->{$method}(...$parameters);
    }
}