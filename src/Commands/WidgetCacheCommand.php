<?php

declare(strict_types=1);

namespace Honed\Widget\Commands;

use Honed\Widget\WidgetServiceProvider;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'widget:cache')]
class WidgetCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widget:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover and cache the widgets for the application.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->callSilent('widget:clear');

        file_put_contents(
            $this->laravel->getCachedWidgetsPath(),
            '<?php return '.var_export($this->getWidgets(), true).';'
        );

        $this->components->info('Widgets cached successfully.');
    }

    /**
     * Get all of the events and listeners configured for the application.
     *
     * @return array<string, class-string<\Honed\Widget\Widget>>
     */
    protected function getWidgets(): array
    {
        $widgets = [];

        foreach ($this->laravel->getProviders(WidgetServiceProvider::class) as $provider) {
            /** @var array<string, class-string<\Honed\Widget\Widget>> */
            $providerWidgets = array_merge_recursive($provider->shouldDiscoverWidgets() ? $provider->discoverWidgets() : [], $provider->widgets());

            $widgets = array_merge($widgets, $providerWidgets);
        }

        return $widgets;
    }
}
