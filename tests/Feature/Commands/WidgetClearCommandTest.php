<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;

beforeEach(function () {
    $this->artisan('widget:cache');
});

it('clears', function () {
    $path = App::getCachedWidgetsPath();

    $this->assertFileExists($path);

    $this->artisan('widget:clear');

    $this->assertFileDoesNotExist($path);
});
