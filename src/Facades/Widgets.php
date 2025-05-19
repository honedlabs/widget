<?php

namespace Honed\Widget\Facades;

use Honed\Widget\WidgetManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Honed\Widget\Drivers\Decorator driver(string|null $name = null) Get a Widget driver instance by name.
 * @method static \Honed\Widget\Drivers\Decorator get(string $name) Attempt to get the driver from the local cache.
 * @method static \Honed\Widget\WidgetManager useMorphMap(bool $value = true) Specify that the Eloquent morph map should be used when serializing.
 * @method static string serializeScope(mixed $scope) Serialize the given scope for storage.
 * @method static void resolveScopeUsing(callable(string): mixed $resolver) Set the default scope resolver.
 * @method static array<string, mixed>|null getConfig(string $name) Get the driver configuration.
 * @method static string getDefaultDriver() Get the default driver name.
 * @method static void setDefaultDriver(string $name) Set the default driver name.
 * @method static \Honed\Widget\WidgetManager forgetDriver(string|array<int, string>|null $name = null) Unset the given driver instances.
 * @method static \Honed\Widget\WidgetManager forgetDrivers() Forget all of the resolved driver instances.
 * @method static \Honed\Widget\WidgetManager extend(string $driver, \Closure(\Illuminate\Contracts\Container\Container, array<string, mixed>):mixed $callback) Register a custom driver creator Closure.
 * @method static \Honed\Widget\WidgetManager setContainer(\Illuminate\Container\Container $container) Set the container instance used by the manager.
 * @method static mixed inertia(string|null $scope = null, string|null $group = null) Get the widgets for an inertia page.
 * @method static mixed get(string|null $scope = null, string|null $group = null) Get all widgets for the given scope and group.
 * @method static mixed all(string|null $scope = null) Get all widgets for the given scope.
 * @method static bool has(string $key, string|null $scope = null, string|null $group = null) Determine if a widget exists in the given scope and group.
 * @method static mixed put(string $key, mixed $value, string|null $scope = null, string|null $group = null) Store a widget value in the given scope and group.
 * @method static mixed increment(string $key, int|float $amount = 1, string|null $scope = null, string|null $group = null) Increment the value of a widget in the given scope and group.
 * @method static mixed decrement(string $key, int|float $amount = 1, string|null $scope = null, string|null $group = null) Decrement the value of a widget in the given scope and group.
 * @method static bool forget(string $key, string|null $scope = null, string|null $group = null) Remove a widget from the given scope and group.
 * @method static bool flush(string|null $scope = null, string|null $group = null) Remove all widgets from the given scope and group.
 *
 * @see \Honed\Widget\WidgetManager
 */
class Widgets extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return \Honed\Widget\WidgetManager
     */
    public static function getFacadeRoot()
    {
        // @phpstan-ignore-next-line
        return parent::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WidgetManager::class;
    }
}
