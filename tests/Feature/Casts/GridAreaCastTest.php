<?php

declare(strict_types=1);

namespace Tests\Feature\Casts;

use App\Models\User;
use Honed\Widget\Casts\GridAreaCast;

beforeEach(function () {
    $this->cast = new GridAreaCast();
});

it('handles null values', function () {
    expect($this->cast)
        ->get(new User(), 'cast', null, [])->toBeNull();
});

it('handles non-string values', function () {
    $cast = 10;

    expect($this->cast)
        ->get(new User(), 'cast', $cast, [])->toBeNull();
});

it('handles string values', function () {
    expect($this->cast)
        ->get(new User(), 'cast', 'a1:a1', [])->toBe('1 / 1 / 2 / 2')
        ->get(new User(), 'cast', 'b2:b2', [])->toBe('2 / 2 / 3 / 3')
        ->get(new User(), 'cast', 'a1:b2', [])->toBe('1 / 1 / 3 / 3')
        ->get(new User(), 'cast', 'a2:a2', [])->toBe('2 / 1 / 3 / 2');
});
