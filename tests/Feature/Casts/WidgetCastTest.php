<?php

declare(strict_types=1);

namespace Tests\Feature\Casts;

use App\Models\User;
use App\Widgets\TeamMembersWidget;
use App\Widgets\UserCountWidget;
use Honed\Widget\Casts\WidgetCast;

beforeEach(function () {
    $this->artisan('widget:cache');

    $this->cast = new WidgetCast();
});

it('handles null values', function () {
    expect($this->cast)
        ->get(new User(), 'cast', null, [])->toBeNull()
        ->set(new User(), 'cast', null, [])->toBeNull();
});

it('handles widget instances', function () {
    $widget = UserCountWidget::make();

    expect($this->cast)
        ->get(new User(), 'cast', $widget->getName(), [])->toBeInstanceOf(UserCountWidget::class)
        ->set(new User(), 'cast', $widget, [])->toBe($widget->getName());
});

it('handles widget class strings', function () {
    $widget = TeamMembersWidget::class;

    expect($this->cast)
        ->get(new User(), 'cast', $widget, [])->toBeInstanceOf(TeamMembersWidget::class)
        ->set(new User(), 'cast', $widget, [])->toBe($widget);
});
