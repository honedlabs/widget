<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\TeamMembersWidget;
use App\Widgets\UserCountWidget;
use Honed\Widget\Facades\Widgets;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->driver = Widgets::store('database')->getDriver();

    $this->table = config('widget.drivers.database.table');
});

it('gets widgets', function () {
    DB::table($this->table)->insert([
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);

    assertDatabaseCount($this->table, 1);

    expect($this->driver->get($this->user))->toEqualCanonicalizing([
        (object) [
            'id' => 1,
            'widget' => Widgets::serializeWidget(UserCountWidget::make()),
            'scope' => Widgets::serializeScope($this->user),
            'data' => json_encode(['count' => 10]),
            'position' => null,
            'created_at' => null,
            'updated_at' => null,
        ],
    ]);

    assertDatabaseHas($this->table, [
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);
});

it('creates widgets', function () {
    assertDatabaseCount($this->table, 0);

    $this->driver->set(UserCountWidget::class, $this->user, ['count' => 10]);

    assertDatabaseCount($this->table, 1);

    assertDatabaseHas($this->table, [
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);
});

it('updates widgets', function () {
    DB::table($this->table)->insert([
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);

    assertDatabaseCount($this->table, 1);
    assertDatabaseHas($this->table, [
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);

    $this->driver->update(UserCountWidget::class, $this->user, ['count' => 20]);

    assertDatabaseCount($this->table, 1);
    assertDatabaseHas($this->table, [
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 20]),
    ]);
});

it('deletes widgets', function () {
    DB::table($this->table)->insert([
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);

    assertDatabaseCount($this->table, 1);
    assertDatabaseHas($this->table, [
        'widget' => Widgets::serializeWidget(UserCountWidget::make()),
        'scope' => Widgets::serializeScope($this->user),
        'data' => json_encode(['count' => 10]),
    ]);

    $this->driver->delete(TeamMembersWidget::class, $this->user);

    assertDatabaseCount($this->table, 1);

    $this->driver->delete(UserCountWidget::class, User::factory()->create());

    assertDatabaseCount($this->table, 1);

    $this->driver->delete(UserCountWidget::class, $this->user);

    assertDatabaseCount($this->table, 0);
});
