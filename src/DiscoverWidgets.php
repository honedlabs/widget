<?php

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
     * @param  string  $basePath
     * @return array
     */
    public static function within($widgetPath, $basePath)
    {
        if (Arr::wrap($widgetPath) === []) {
            return [];
        }

        $files = Finder::create()->files()->in($widgetPath);

        /** @var array<int, \Honed\Widget\Widget> $widgets */
        $widgets = [];

        foreach ($files as $file) {
            $widget = static::classFromFile($file, $basePath);

            if (static::invalidWidget($widget)) {
                continue;
            }

            /** @var \Honed\Widget\Widget $widget */
            $widget = App::make($widget);

            $widgets[$widget->getName()] = $widget;
        }

        return $widgets;
    }

    /**
     * Determine if the widget is invalid.
     * 
     * @param  class-string  $widget
     * @return bool
     */
    protected static function invalidWidget($widget)
    {
        return ! class_exists($widget) 
            || ! is_subclass_of($widget, Widget::class)
            || ! (new ReflectionClass($widget))->isInstantiable();
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $basePath
     * @return class-string
     */
    protected static function classFromFile(SplFileInfo $file, $basePath)
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

    /**
     * Specify a callback to be used to guess class names.
     *
     * @param  callable(SplFileInfo, string): class-string  $callback
     * @return void
     */
    public static function guessClassNamesUsing(callable $callback)
    {
        static::$guessClassNamesUsingCallback = $callback;
    }
}