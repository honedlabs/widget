<?php

declare(strict_types=1);

namespace Honed\Widget;

use Honed\Widget\Commands\WidgetCacheCommand;
use Honed\Widget\Commands\WidgetClearCommand;
use Honed\Widget\Commands\WidgetListCommand;
use Honed\Widget\Commands\WidgetMakeCommand;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * The widgets to register.
     *
     * @var array<string, class-string<Widget>>
     */
    protected $widgets = [];

    /**
     * Indicates if widgets should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverWidgets = true;

    /**
     * The configured widget discovery paths.
     *
     * @var iterable<int, string>|null
     */
    protected static $widgetDiscoveryPaths;

    /**
     * The base path to be used during widget discovery.
     *
     * @var string|null
     */
    protected static $widgetDiscoveryBasePath;

    /**
     * Add the given widget discovery paths to the application's widget discovery paths.
     *
     * @param  string|iterable<int, string>  $paths
     */
    public static function addWidgetDiscoveryPaths(iterable|string $paths): void
    {
        static::$widgetDiscoveryPaths = (new LazyCollection(static::$widgetDiscoveryPaths))
            ->merge(is_string($paths) ? [$paths] : $paths)
            ->unique()
            ->values();
    }

    /**
     * Get the widget discovery paths.
     *
     * @return iterable<int, string>
     */
    public static function getWidgetDiscoveryPaths(): iterable
    {
        return static::$widgetDiscoveryPaths ?? [];
    }

    /**
     * Set the widget discovery paths.
     *
     * @param  iterable<int, string>  $paths
     */
    public static function setWidgetDiscoveryPaths(iterable $paths): void
    {
        static::$widgetDiscoveryPaths = $paths;
    }

    /**
     * Get the base path to be used during widget discovery.
     */
    public static function getWidgetDiscoveryBasePath(): string
    {
        return static::$widgetDiscoveryBasePath ?? base_path();
    }

    /**
     * Set the base path to be used during widget discovery.
     */
    public static function setWidgetDiscoveryBasePath(string $path): void
    {
        static::$widgetDiscoveryBasePath = $path;
    }

    /**
     * Disable widget discovery for the application.
     */
    public static function disableWidgetDiscovery(): void
    {
        static::$shouldDiscoverWidgets = false;
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->getApp()->singleton(WidgetManager::class, fn ($app) => new WidgetManager($app));

        $this->mergeConfigFrom(__DIR__.'/../config/widget.php', 'widget');

        App::macro('getCachedWidgetsPath', function (): string {
            /** @var Application $this */

            return $this->normalizeCachePath('APP_WIDGETS_CACHE', 'cache/widgets.php'); // @phpstan-ignore-line method.protected
        });

        App::macro('widgetsAreCached', function (): bool {
            /** @var Application $this */

            /** @var \Illuminate\Filesystem\Filesystem $files */
            $files = $this->files; // @phpstan-ignore-line varTag.nativeType

            return $files->exists($this->getCachedWidgetsPath());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->optimizes(WidgetCacheCommand::class);

            $this->offerPublishing();

            $this->commands([
                WidgetCacheCommand::class,
                WidgetClearCommand::class,
                WidgetListCommand::class,
                WidgetMakeCommand::class,
            ]);
        }
    }

    /**
     * Get the discovered widgets for the application.
     *
     * @return array<string, class-string<Widget>>
     */
    public function getWidgets(): array
    {
        if ($this->getApp()->widgetsAreCached()) {
            /** @var array<string, class-string<Widget>>|null */
            $cache = require $this->getApp()->getCachedWidgetsPath();

            return is_array($cache) ? $cache : [];
        }

        return array_merge_recursive(
            $this->discoveredWidgets(),
            $this->widgets()
        );
    }

    /**
     * Register a widget with the service provider.
     *
     * @param  class-string<Widget>|Widget  $widget
     */
    public function addWidget(string|Widget $widget): void
    {
        $widget = is_string($widget) ? $this->getApp()->make($widget) : $widget;

        if (! isset($this->widgets[$widget->getName()])) {
            // @phpstan-ignore-next-line assign.propertyType
            $this->widgets[$widget->getName()] = get_class($widget);
        }
    }

    /**
     * Get the widgets that should be cached, keyed by the widget name.
     *
     * @return array<string, class-string<Widget>>
     */
    public function widgets(): array
    {
        return $this->widgets;
    }

    /**
     * Determine if widgets should be automatically discovered.
     */
    public function shouldDiscoverWidgets(): bool
    {
        return get_class($this) === __CLASS__ && static::$shouldDiscoverWidgets;
    }

    /**
     * Discover the widgets for the application.
     *
     * @return array<string, class-string<Widget>>
     */
    public function discoverWidgets(): array
    {
        return (new LazyCollection($this->discoverWidgetsWithin()))
            ->flatMap(static function (string $directory): array {
                $result = glob($directory, GLOB_ONLYDIR);

                return $result !== false ? $result : [];
            })
            ->reject(static function (string $directory): bool {
                return ! is_dir($directory);
            })
            ->pipe(static fn ($directories) => DiscoverWidgets::within(
                $directories->all(),
                static::getWidgetDiscoveryBasePath(),
            ));
    }

    /**
     * Register the migrations and publishing for the package.
     */
    protected function offerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/widget.php' => config_path('widget.php'),
        ], 'widget-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'widget-migrations');
    }

    /**
     * Get the discovered widgets for the application.
     *
     * @return array<string, class-string<Widget>>
     */
    protected function discoveredWidgets(): array
    {
        return $this->shouldDiscoverWidgets()
            ? $this->discoverWidgets()
            : [];
    }

    /**
     * Get the directories that should be used to discover widgets.
     *
     * @return iterable<int, string>
     */
    protected function discoverWidgetsWithin(): iterable
    {
        return static::$widgetDiscoveryPaths ?: [
            $this->getApp()->path('Widgets'),
        ];

    }

    /**
     * Get the application instance.
     */
    protected function getApp(): Application
    {
        /** @var Application */
        return $this->app;
    }
}
