<?php

namespace Honed\Widget;

use Honed\Widget\Commands\WidgetCacheCommand;
use Honed\Widget\Commands\WidgetClearCommand;
use Honed\Widget\Commands\WidgetListCommand;
use Honed\Widget\Commands\WidgetMakeCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * The widgets to register.
     *
     * @var array<int, class-string<\Honed\Widget\Widget>>
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
     * Register services.
     */
    public function register(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        $app->singleton(WidgetManager::class, fn ($app) => new WidgetManager($app));

        $this->mergeConfigFrom(__DIR__.'/../config/widget.php', 'widget');

        App::macro('getCachedWidgetsPath', function () {
            /** @var \Illuminate\Foundation\Application $this */
            return $this->normalizeCachePath('APP_WIDGETS_CACHE', 'cache/widgets.php');
        });

        App::macro('widgetsAreCached', function () {
            /** @var \Illuminate\Foundation\Application $this */
            return $this->files->exists($this->getCachedWidgetsPath());
        });

        $this->booting(function () {
            $widgets = $this->getWidgets();

            foreach ($widgets as $widget) {

            }

            foreach ($this->widgets as $widget) {
                $this->app->make($widget)::register();
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->optimizes(WidgetCacheCommand::class);

        if ($this->app->runningInConsole()) {
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
     * Register the migrations and publishing for the package.
     *
     * @return void
     */
    protected function offerPublishing()
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
     * @return array
     */
    public function getWidgets()
    {
        if ($this->app->widgetsAreCached()) {
            $cache = require $this->app->getCachedWidgetsPath();

            return $cache[get_class($this)] ?? [];
        } else {
            return array_merge_recursive(
                $this->discoveredWidgets(),
                $this->widgets()
            );
        }
    }

    /**
     * Get the widgets that should be cached.
     *
     * @return array<int, class-string<\Honed\Widget\Widget>>
     */
    public function widgets()
    {
        return $this->widgets;
    }

    /**
     * Get the discovered widgets for the application.
     *
     * @return array
     */
    protected function discoveredWidgets()
    {
        return $this->shouldDiscoverWidgets()
            ? $this->discoverWidgets()
            : [];
    }

    /**
     * Determine if widgets should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverWidgets()
    {
        return get_class($this) === __CLASS__ && static::$shouldDiscoverWidgets;
    }

    /**
     * Discover the widgets for the application.
     *
     * @return array
     */
    public function discoverWidgets()
    {
        return (new LazyCollection($this->discoverWidgetsWithin()))
            ->flatMap(function ($directory) {
                return glob($directory, GLOB_ONLYDIR);
            })
            ->reject(function ($directory) {
                return ! is_dir($directory);
            })
            ->pipe(fn ($directories) => DiscoverWidgets::within(
                $directories->all(),
                $this->widgetDiscoveryBasePath(),
            ));
    }

    /**
     * Get the directories that should be used to discover widgets.
     *
     * @return iterable<int, string>
     */
    protected function discoverWidgetsWithin()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        return static::$widgetDiscoveryPaths ?: [
            $app->path('Widgets'),
        ];
    }

    /**
     * Add the given widget discovery paths to the application's widget discovery paths.
     *
     * @param  string|iterable<int, string>  $paths
     * @return void
     */
    public static function addWidgetDiscoveryPaths(iterable|string $paths)
    {
        static::$widgetDiscoveryPaths = (new LazyCollection(static::$widgetDiscoveryPaths))
            ->merge(is_string($paths) ? [$paths] : $paths)
            ->unique()
            ->values();
    }

    /**
     * Set the globally configured widget discovery paths.
     *
     * @param  iterable<int, string>  $paths
     * @return void
     */
    public static function setWidgetDiscoveryPaths($paths)
    {
        static::$widgetDiscoveryPaths = $paths;
    }

    /**
     * Get the base path to be used during widget discovery.
     *
     * @return string
     */
    protected function widgetDiscoveryBasePath()
    {
        return base_path();
    }

    /**
     * Disable widget discovery for the application.
     *
     * @return void
     */
    public static function disableWidgetDiscovery()
    {
        static::$shouldDiscoverWidgets = false;
    }
}
