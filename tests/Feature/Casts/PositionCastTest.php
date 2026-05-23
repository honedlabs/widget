<?php

declare(strict_types=1);

namespace Tests\Feature\Casts;

use App\Models\User;
use Honed\Widget\Casts\PositionCast;

beforeEach(function () {
    $this->cast = new PositionCast();
});

it('handles null values', function () {
    expect($this->cast->set(new User(), 'position', null, []))->toBeNull();
});

it('handles string values', function () {
    $position = 'a1:b2';

    expect($this->cast->set(new User(), 'position', $position, []))->toBe($position);
});

it('handles array values', function () {
    $position = [0, 0, 1, 1];

    expect($this->cast->set(new User(), 'position', $position, []))->toBe('a1:b2');
});
