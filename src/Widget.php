<?php

declare(strict_types=1);

namespace Honed\Widget;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Widget
{
    /**
     * The unique name of the widget across your application's widgets.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The callback to guess the widget name.
     *
     * @var (callable(static):string)|null
     */
    protected static $guessWidgetNameUsing;

    /**
     * Retrieve the value of the widget.
     *
     * @return mixed
     */
    abstract public function getValue();

    /**
     * Create a new widget instance.
     */
    public static function make(): static
    {
        return resolve(static::class);
    }

    /**
     * Set the callback to guess the widget name.
     *
     * @param  callable(static):string  $callback
     */
    public static function guessWidgetNameUsing(callable $callback): void
    {
        static::$guessWidgetNameUsing = $callback;
    }

    /**
     * Get the name of the widget to be used.
     */
    public function getName(): string
    {
        return $this->name ?? $this->guessWidgetName();
    }

    // public static function register()
    // {
    //     App::getProvider(WidgetServiceProvider::class)->add
    // }

    /**
     * Guess the widget name.
     */
    public function guessWidgetName(): string
    {
        if (static::$guessWidgetNameUsing) {
            return call_user_func(static::$guessWidgetNameUsing, $this);
        }

        return Str::of(static::class)
            ->basename()
            ->value();
    }
}
