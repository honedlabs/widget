<?php

namespace Honed\Widget\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'widget:clear')]
class WidgetClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'widget:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the cached application widgets.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new config clear command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->getCachedWidgetsPath());

        $this->components->info('Cached widgets cleared successfully.');
    }
}
