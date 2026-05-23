<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\UserCountWidget;
use Honed\Widget\Events\WidgetDeleted;
use Honed\Widget\Facades\Widgets;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();

    $this->user = User::factory()->create();

    $this->widget = Widgets::serializeWidget(new UserCountWidget());

    $this->scope = Widgets::serializeScope($this->user);
});

afterEach(function () {
    Event::assertDispatched(WidgetDeleted::class, function (WidgetDeleted $event) {
        return true;
    });
});

it('dispatches event', function () {
    WidgetDeleted::dispatch(
        $this->widget, $this->scope
    );
});

it('dispatches event when deleting widget', function () {
    Widgets::delete(UserCountWidget::class, $this->user);
});
