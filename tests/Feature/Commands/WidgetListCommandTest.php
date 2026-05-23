<?php

declare(strict_types=1);

beforeEach(function () {
    $this->artisan('widget:cache');
});

afterEach(function () {
    $this->artisan('widget:clear');
});

it('caches', function () {
    $this->artisan('widget:list')->assertSuccessful();
});
