<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

it('makes', function () {
    $this->artisan('widget:cache')->assertSuccessful();
});