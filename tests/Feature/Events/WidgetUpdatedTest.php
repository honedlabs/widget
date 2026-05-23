<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\UserCountWidget;
use Honed\Widget\Events\WidgetUpdated;
use Honed\Widget\Facades\Widgets;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();

    $this->user = User::factory()->create();

    $this->widget = Widgets::serializeWidget(new UserCountWidget());

    $this->scope = Widgets::serializeScope($this->user);
});

afterEach(function () {
    Event::assertDispatched(WidgetUpdated::class, function (WidgetUpdated $event) {
        return true;
    });
});

it('dispatches event', function () {
    WidgetUpdated::dispatch(
        $this->widget, $this->scope, ['count' => 10]
    );
});

it('dispatches event when updating widget', function () {
    Widgets::update(UserCountWidget::class, $this->user, ['count' => 10]);
});
