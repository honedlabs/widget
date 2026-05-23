<?php

declare(strict_types=1);

namespace Honed\Widget\Commands;

use Honed\Widget\WidgetServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
                            {--json : Output the widgets as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List the application's widgets";

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $widgets = $this->getWidgets()->sortKeys();

        if ($widgets->isEmpty()) {
            if ($this->option('json')) {
                $this->output->writeln('[]');
            } else {
                $this->components->info("Your application doesn't have any widgets matching the given criteria.");
            }

            return;
        }

        if ($this->shouldDisplayJson()) {
            $this->displayJson($widgets);
        } else {
            $this->displayForCli($widgets);
        }
    }

    /**
     * Display widgets in JSON.
     *
     * @param  Collection<string, class-string<\Honed\Widget\Widget>>  $widgets
     */
    protected function displayJson(Collection $widgets): void
    {
        $data = $widgets->map(function ($widget) {
            return [
                'widget' => $widget,
            ];
        })->values();

        $this->output->writeln($data->toJson(JSON_PRETTY_PRINT));
    }

    /**
     * Display the widgets for the CLI.
     *
     * @param  Collection<string, class-string<\Honed\Widget\Widget>>  $widgets
     */
    protected function displayForCli(Collection $widgets): void
    {
        $this->newLine();

        $widgets->each(function ($class, $name) {
            $this->components->twoColumnDetail($name, $class);
        });

        $this->newLine();
    }

    /**
     * Get all of the widgets configured for the application.
     *
     * @return Collection<string, class-string<\Honed\Widget\Widget>>
     */
    protected function getWidgets(): Collection
    {
        $widgets = new Collection($this->getRegisteredWidgets());

        if ($this->filteringByWidget()) {
            $widgets = $this->filterWidgets($widgets);
        }

        return $widgets;
    }

    /**
     * Get the registered widgets.
     *
     * @return array<string, class-string<\Honed\Widget\Widget>>
     */
    protected function getRegisteredWidgets(): array
    {
        $widgets = [];

        foreach ($this->laravel->getProviders(WidgetServiceProvider::class) as $provider) {
            /** @var array<string, class-string<\Honed\Widget\Widget>> */
            $providerWidgets = array_merge_recursive($provider->shouldDiscoverWidgets() ? $provider->discoverWidgets() : [], $provider->widgets());

            $widgets = array_merge($widgets, $providerWidgets);
        }

        return $widgets;
    }

    /**
     * Filter the given widgets using the provided widget name filter.
     *
     * @param  Collection<string, class-string<\Honed\Widget\Widget>>  $widgets
     * @return Collection<string, class-string<\Honed\Widget\Widget>>
     */
    protected function filterWidgets(Collection $widgets): Collection
    {
        if (! $widgetName = $this->option('widget')) {
            return $widgets;
        }

        return $widgets->filter(
            static fn ($widget, $name) => str_contains($name, $widgetName)
        );
    }

    /**
     * Determine whether the user is filtering by a widget name.
     */
    protected function filteringByWidget(): bool
    {
        return filled($this->option('widget'));
    }

    /**
     * Determine whether the user is displaying the widgets as JSON.
     */
    protected function shouldDisplayJson(): bool
    {
        return (bool) $this->option('json');
    }
}
