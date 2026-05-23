<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;

beforeEach(function () {
    $this->artisan('widget:clear');
});

afterEach(function () {
    $this->artisan('widget:clear');
});

it('caches', function () {
    $path = App::getCachedWidgetsPath();

    $this->assertFileDoesNotExist($path);

    $this->artisan('widget:cache');

    $this->assertFileExists($path);
});
