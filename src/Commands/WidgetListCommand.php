<?php

namespace Honed\Widget\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Collection;
use ReflectionFunction;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'widget:list')]
class WidgetListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widget:list
                            {--widget= : Filter the widgets by name}
                            {--json : Output the widgets and listeners as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List the application's widgets";

    /**
     * The events dispatcher resolver callback.
     *
     * @var \Closure|null
     */
    protected static $widgetsResolver;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $widgets = $this->getWidgets()->sortKeys();

        dd($widgets);
        if ($widgets->isEmpty()) {
            if ($this->option('json')) {
                $this->output->writeln('[]');
            } else {
                $this->components->info("Your application doesn't have any widgets matching the given criteria.");
            }

            return;
        }

        if ($this->option('json')) {
            $this->displayJson($widgets);
        } else {
            $this->displayForCli($widgets);
        }
    }

    /**
     * Display events and their listeners in JSON.
     *
     * @param  \Illuminate\Support\Collection  $widgets
     * @return void
     */
    protected function displayJson($widgets)
    {
        $data = $widgets->map(function ($listeners, $widget) {
            return [
                'widget' => strip_tags($this->appendWidgetInterfaces($widget)),
                'listeners' => collect($listeners)->map(fn ($listener) => strip_tags($listener))->values()->all(),
            ];
        })->values();

        $this->output->writeln($data->toJson());
    }

    /**
     * Display the events and their listeners for the CLI.
     *
     * @return void
     */
    protected function displayForCli(Collection $events)
    {
        $this->newLine();

        $events->each(function ($listeners, $event) {
            $this->components->twoColumnDetail($this->appendEventInterfaces($event));
            $this->components->bulletList($listeners);
        });

        $this->newLine();
    }

    /**
     * Get all of the events and listeners configured for the application.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getWidgets()
    {
        $widgets = new Collection($this->getListenersOnDispatcher());

        if ($this->filteringByWidget()) {
            $widgets = $this->filterWidgets($widgets);
        }

        return $widgets;
    }

    /**
     * Get the event / listeners from the dispatcher object.
     *
     * @return array
     */
    protected function getListenersOnDispatcher()
    {
        $widgets = [];

        foreach ($this->getRawListeners() as $widget => $rawListeners) {
            dd($widget, $rawListeners);
            foreach ($rawListeners as $rawListener) {
                if (is_string($rawListener)) {
                    $widgets[$widget][] = $this->appendListenerInterfaces($rawListener);
                } elseif ($rawListener instanceof Closure) {
                    $widgets[$widget][] = $this->stringifyClosure($rawListener);
                } elseif (is_array($rawListener) && count($rawListener) === 2) {
                    if (is_object($rawListener[0])) {
                        $rawListener[0] = get_class($rawListener[0]);
                    }

                    $widgets[$widget][] = $this->appendListenerInterfaces(implode('@', $rawListener));
                }
            }
        }

        return $widgets;
    }

    /**
     * Add the event implemented interfaces to the output.
     *
     * @param  string  $widget
     * @return string
     */
    protected function appendWidgetInterfaces($widget)
    {
        if (! class_exists($widget)) {
            return $widget;
        }

        $interfaces = class_implements($widget);

        if (in_array(ShouldBroadcast::class, $interfaces)) {
            $widget .= ' <fg=bright-blue>(ShouldBroadcast)</>';
        }

        return $widget;
    }

    /**
     * Get a displayable string representation of a Closure listener.
     *
     * @return string
     */
    protected function stringifyClosure(Closure $rawListener)
    {
        $reflection = new ReflectionFunction($rawListener);

        $path = str_replace([base_path(), DIRECTORY_SEPARATOR], ['', '/'], $reflection->getFileName() ?: '');

        return 'Closure at: '.$path.':'.$reflection->getStartLine();
    }

    /**
     * Filter the given events using the provided event name filter.
     *
     * @param  \Illuminate\Support\Collection  $widgets
     * @return \Illuminate\Support\Collection
     */
    protected function filterWidgets($widgets)
    {
        if (! $widgetName = $this->option('widget')) {
            return $widgets;
        }

        return $widgets->filter(
            fn ($listeners, $widget) => str_contains($widget, $widgetName)
        );
    }

    /**
     * Determine whether the user is filtering by an event name.
     *
     * @return bool
     */
    protected function filteringByWidget()
    {
        return filled($this->option('widget'));
    }
}
