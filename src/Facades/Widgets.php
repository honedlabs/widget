<?php

declare(strict_types=1);

namespace Honed\Widget\Facades;

use Honed\Widget\WidgetManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Honed\Widget\Drivers\Decorator driver(string|null $name = null) Get a Widget driver instance by name.
 * @method static \Honed\Widget\Drivers\Decorator store(string|null $store = null) Get a widget store instance.
 * @method static \Honed\Widget\Drivers\Decorator get(string $name) Attempt to get the driver from the local cache.
 * @method static class-string<\Illuminate\Database\Eloquent\Model> model() Get the widget model class.
 * @method static \Honed\Widget\Widget|null make(mixed $widget) Get an instance of a widget class by the cached name.
 * @method static \Honed\Widget\WidgetManager useMorphMap(bool $value = true) Specify that the Eloquent morph map should be used when serializing.
 * @method static bool usesMorphMap() Determine if the Eloquent morph map should be used when serializing.
 * @method static string serializeScope(mixed $scope) Serialize the given scope for storage.
 * @method static string serializeWidget(mixed $widget) Serialize the widget for storage.
 * @method static void resolveScopeUsing(callable $resolver) Set the default scope resolver.
 * @method static callable defaultScopeResolver(string $driver) Get the default scope resolver.
 * @method static string convertToGridArea(string $position) Convert a grid position to a CSS grid area.
 * @method static string convertToPosition(int $x1 = 0, int $y1 = 0, int $x2 = 0, int $y2 = 0) Convert a set of grid positions to a position string.
 * @method static \Illuminate\Config\Repository getConfig() Get the config instance from the container.
 * @method static string getDefaultDriver() Get the default driver name.
 * @method static void setDefaultDriver(string $name) Set the default driver name.
 * @method static \Honed\Widget\WidgetManager forgetDriver(string|array<int, string>|null $name = null) Unset the given driver instances.
 * @method static \Honed\Widget\WidgetManager forgetDrivers() Forget all of the resolved driver instances.
 * @method static \Honed\Widget\WidgetManager extend(string $driver, \Closure $callback) Register a custom driver creator Closure.
 * @method static \Honed\Widget\WidgetManager setContainer(\Illuminate\Contracts\Container\Container $container) Set the container instance used by the manager.
 * @method static \Illuminate\Contracts\Events\Dispatcher getDispatcher() Get the event dispatcher instance from the container.
 * @method static \Illuminate\Database\DatabaseManager getDatabaseManager() Get the database manager instance from the container.
 * @method static \Illuminate\Cookie\CookieJar getCookieJar() Get the cookie jar instance from the container.
 * @method static \Illuminate\Http\Request getRequest() Get the request instance from the container.
 * @method static \Illuminate\Session\SessionManager getSession() Get the session manager instance from the container.
 *
 * @see WidgetManager
 */
class Widgets extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return WidgetManager
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
