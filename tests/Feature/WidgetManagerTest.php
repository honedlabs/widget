<?php

declare(strict_types=1);

use App\Widgets\UserCountWidget;
use Honed\Widget\Facades\Widgets;
use Honed\Widget\Drivers\Decorator;
use Honed\Widget\Drivers\ArrayDriver;
use Honed\Widget\Drivers\DatabaseDriver;

beforeEach(function () {
    $this->artisan('widget:cache');
});

it('has model', function () {
    expect(Widgets::model())
        ->toBe(config('widget.model'));

    config()->set('widget.model', 'Widget');

    expect(Widgets::model())
        ->toBe('Widget');
});

it('makes widgets', function () {
    expect(Widgets::make('count'))
        ->toBeInstanceof(UserCountWidget::class);

    expect(Widgets::make('missing'))
        ->toBeNull();
});

it('gets drivers', function () {
    expect(Widgets::driver())
        ->toBeInstanceOf(Decorator::class)
        ->getDriver()->toBeInstanceOf(DatabaseDriver::class);

    expect(Widgets::driver('array'))
        ->toBeInstanceOf(Decorator::class)
        ->getDriver()->toBeInstanceOf(ArrayDriver::class);
});

it('throws error if driver not found', function () {
    Widgets::driver('missing');
})->throws(InvalidArgumentException::class);