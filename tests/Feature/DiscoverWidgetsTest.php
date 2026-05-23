<?php

declare(strict_types=1);

use App\Widgets\TeamMembersWidget;
use App\Widgets\UserCountWidget;
use Honed\Widget\DiscoverWidgets;
use Illuminate\Support\Str;

use function Orchestra\Testbench\workbench_path;

beforeEach(function () {});

it('returns empty when no paths are provided', function () {
    expect(DiscoverWidgets::within([], workbench_path()))
        ->toBeArray()
        ->toBeEmpty();
});

it('rejects invalid widgets', function () {
    expect(DiscoverWidgets::within([workbench_path('app/Models')], workbench_path()))
        ->toBeArray()
        ->toBeEmpty();
});

it('finds widgets', function () {
    expect(DiscoverWidgets::within([workbench_path('app/Widgets')], workbench_path()))
        ->toBeArray()
        ->toHaveCount(2)
        ->toHaveKeys([
            TeamMembersWidget::make()->getName(),
            UserCountWidget::make()->getName(),
        ]);
});

it('can guess class names', function () {
    DiscoverWidgets::guessClassNamesUsing(function (SplFileInfo $file, string $basePath) {
        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return ucfirst(Str::camel(str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        )));
    });

    expect(DiscoverWidgets::within([workbench_path('app/Widgets')], workbench_path()))
        ->toBeArray()
        ->toHaveCount(2)
        ->toHaveKeys([
            TeamMembersWidget::make()->getName(),
            UserCountWidget::make()->getName(),
        ]);
});
