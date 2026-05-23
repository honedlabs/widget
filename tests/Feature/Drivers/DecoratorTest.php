<?php

declare(strict_types=1);

use App\Models\User;
use Honed\Widget\Facades\Widgets;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->decorator = Widgets::store();

    $this->driver = $this->decorator->getDriver();

    Event::fake();

    $this->artisan('widget:cache');
});

it('gets widgets', function () {
    $this->driver->set('user.count', $this->user, ['count' => 10]);

    expect($this->driver->get($this->user))->toEqual([
        [
            'widget' => 'user.count',
            'scope' => $this->user,
            'data' => ['count' => 10],
        ],
    ]);
})->todo();
