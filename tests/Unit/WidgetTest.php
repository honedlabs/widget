<?php

declare(strict_types=1);

use Workbench\App\Models\User;
use Honed\Widget\WidgetServiceProvider;

use function Orchestra\Testbench\workbench_path;

beforeEach(function () {
    WidgetServiceProvider::setWidgetDiscoveryPaths([
        workbench_path('app/Widgets'),
    ]);
});

it('tests', function () {
    // dd(User::query()->get());
    // dd(app()->getCachedWidgetsPath());
    dd(app()->getCachedWidgetsPath());

    // Widget::for($user)->get();

    // Widget::for($user)->inertia();
});
