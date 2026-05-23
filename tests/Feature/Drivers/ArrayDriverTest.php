<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\TeamMembersWidget;
use App\Widgets\UserCountWidget;
use Honed\Widget\Facades\Widgets;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->driver = Widgets::store('array')->getDriver();
});

it('gets widgets', function () {
    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        [
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => ['count' => 10],
            'position' => null,
        ],
    ]);
});

it('creates widgets', function () {
    expect($this->driver->get($this->user))->toBeEmpty();

    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        [
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => ['count' => 10],
            'position' => null,
        ],
    ]);
});

it('does not update widgets for non-existent scope', function () {
    $this->driver->update(TeamMembersWidget::class, $this->user, ['count' => 20]);

    expect($this->driver->get($this->user))->toBeEmpty();
});

it('does not update widgets for non-existent widget', function () {
    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    $this->driver->update(TeamMembersWidget::class, $this->user, ['count' => 20]);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        [
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => ['count' => 10],
            'position' => null,
        ],
    ]);
});

it('updates widgets', function () {
    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        [
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => ['count' => 10],
            'position' => null,
        ],
    ]);

    $this->driver->update(UserCountWidget::class, $this->user, ['count' => 20]);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        [
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => ['count' => 20],
            'position' => null,
        ],
    ]);
});

it('deletes widgets', function () {
    expect($this->driver->get($this->user))->toBeEmpty();

    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    expect($this->driver->get($this->user))->not->toBeEmpty();

    $this->driver->delete(TeamMembersWidget::class, $this->user);

    expect($this->driver->get($this->user))->not->toBeEmpty();

    $this->driver->delete(UserCountWidget::class, User::factory()->create());

    expect($this->driver->get($this->user))->not->toBeEmpty();

    $this->driver->delete(UserCountWidget::class, $this->user);

    expect($this->driver->get($this->user))->toBeEmpty();
});
