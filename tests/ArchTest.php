<?php

declare(strict_types=1);

use Illuminate\Console\Command;

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('attributes')
    ->expect('Honed\Widget\Attributes')
    ->toBeClasses();

arch('commands')
    ->expect('Honed\Widget\Console\Commands')
    ->toBeClasses()
    ->toExtend(Command::class);

arch('concerns')
    ->expect('Honed\Widget\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Widget\Contracts')
    ->toBeInterfaces();

arch('drivers')
    ->expect('Honed\Widget\Drivers')
    ->toImplement('Honed\Widget\Contracts\Driver');

arch('events')
    ->expect('Honed\Widget\Events')
    ->toBeClasses();

arch('strict')
    ->expect('Honed\Widget')
    ->toUseStrictTypes();
