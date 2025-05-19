<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Widgets'));
});

it('makes', function () {
    $this->artisan('make:widget', [
        'name' => 'UserCountWidget',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Widgets/UserCountWidget.php'));
});

it('widgets for a name', function () {
    $this->artisan('make:widget', [
        '--force' => true,
    ])->expectsQuestion('What should the widget be named?', 'UserCountWidget')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Widgets/UserCountWidget.php'));
});