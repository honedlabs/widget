<?php

declare(strict_types=1);

namespace Honed\Widget;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class DiscoverWidgets
{
    /**
     * The callback to be used to guess class names.
     *
     * @var (callable(SplFileInfo, string): class-string)|null
     */
    public static $guessClassNamesUsingCallback;

    /**
     * Get all of the widgets by searching the given widget directory.
     *
     * @param  array<int, string>|string  $widgetPath
     * @return array<string, class-string<Widget>>
     */
    public static function within(array|string $widgetPath, string $basePath): array
    {
        if (Arr::wrap($widgetPath) === []) {
            return [];
        }

        /**
         * @var array<string, class-string<Widget>>
         */
        $discoveredWidgets = [];

        $files = Finder::create()->files()->in($widgetPath);

        foreach ($files as $file) {
            $widget = static::classFromFile($file, $basePath);

            if (static::invalid($widget)) {
                continue;
            }

            /** @var Widget */
            $widget = App::make($widget);

            if (! isset($discoveredWidgets[$widget->getName()])) {
                $discoveredWidgets[$widget->getName()] = get_class($widget);
            }
        }

        return $discoveredWidgets;
    }

    /**
     * Specify a callback to be used to guess class names.
     *
     * @param  callable(SplFileInfo, string): class-string  $callback
     */
    public static function guessClassNamesUsing(callable $callback): void
    {
        static::$guessClassNamesUsingCallback = $callback;
    }

    /**
     * Determine if the widget is invalid.
     */
    protected static function invalid(string $widget): bool
    {
        return ! class_exists($widget)
            || ! is_subclass_of($widget, Widget::class)
            || ! (new ReflectionClass($widget))->isInstantiable();
    }

    /**
     * Extract the class name from the given file path.
     */
    protected static function classFromFile(SplFileInfo $file, string $basePath): string
    {
        if (static::$guessClassNamesUsingCallback) {
            return call_user_func(static::$guessClassNamesUsingCallback, $file, $basePath);
        }

        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return ucfirst(Str::camel(str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        )));
    }
}
