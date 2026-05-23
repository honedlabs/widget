<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\UserCountWidget;
use Honed\Widget\Facades\Widgets;
use Honed\Widget\QueryBuilder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->connection = DB::connection();

    $this->builder = new QueryBuilder($this->connection);
});

it('has scope where clause', function () {
    $user = User::factory()->create();

    expect($this->builder)
        ->scope($user)->toBe($this->builder)
        ->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($where) => $where
                ->toBeArray()
                ->toEqualCanonicalizing([
                    'type' => 'Basic',
                    'column' => 'scope',
                    'operator' => '=',
                    'value' => Widgets::serializeScope($user),
                    'boolean' => 'and',
                ])
            )
        );
});

it('has widget where clause', function () {
    $widget = UserCountWidget::make();

    expect($this->builder)
        ->widget($widget)->toBe($this->builder)
        ->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($where) => $where
                ->toBeArray()
                ->toEqualCanonicalizing([
                    'type' => 'Basic',
                    'column' => 'widget',
                    'operator' => '=',
                    'value' => Widgets::serializeWidget($widget),
                    'boolean' => 'and',
                ])
            ));
});
