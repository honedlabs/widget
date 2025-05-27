<?php

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
    public function handle()
    {
        $this->callSilent('widget:clear');

        file_put_contents(
            $this->laravel->getCachedWidgetsPath(),
            '<?php return '.var_export($this->getWidgets(), true).';'
        );

        $this->components->info('Widgets cached successfully.');
    }

    /**
     * Get the widgets that should be cached.
     * 
     * @return array
     */
        /**
     * Get all of the events and listeners configured for the application.
     *
     * @return array
     */
    protected function getWidgets()
    {
        $widgets = [];

        foreach ($this->laravel->getProviders(WidgetServiceProvider::class) as $provider) {
            $providerWidgets = array_merge_recursive($provider->shouldDiscoverWidgets() ? $provider->discoverWidgets() : [], $provider->widgets());

            $widgets[get_class($provider)] = $providerWidgets;
        }

        return $widgets;
    }
}