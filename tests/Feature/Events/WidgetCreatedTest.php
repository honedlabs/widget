<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\UserCountWidget;
use Honed\Widget\Events\WidgetCreated;
use Honed\Widget\Facades\Widgets;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();

    $this->user = User::factory()->create();

    $this->widget = Widgets::serializeWidget(new UserCountWidget());

    $this->scope = Widgets::serializeScope($this->user);
});

afterEach(function () {
    Event::assertDispatched(WidgetCreated::class, function (WidgetCreated $event) {
        return true;
    });
});

it('dispatches event', function () {
    WidgetCreated::dispatch(
        $this->widget, $this->scope, ['count' => 10]
    );
});

it('dispatches event when creating widget', function () {
    Widgets::set(UserCountWidget::class, $this->user, ['count' => 10]);
});
