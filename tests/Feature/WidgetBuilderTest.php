<?php

declare(strict_types=1);

use App\Models\User;
use App\Widgets\UserCountWidget;
use Honed\Widget\Facades\Widgets;
use Honed\Widget\Models\Widget;
use Honed\Widget\QueryBuilder;
use Honed\Widget\WidgetBuilder;
use Illuminate\Database\Query\Builder as DatabaseQueryBuilder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->connection = DB::connection();

    $this->builder = (new WidgetBuilder(new QueryBuilder($this->connection)))->setModel(new Widget());
});

it('overrides parent methods', function () {
    expect($this->builder)
        ->getQuery()->toBeInstanceOf(QueryBuilder::class)
        ->setQuery(new DatabaseQueryBuilder(
            $this->connection,
            $this->connection->getQueryGrammar(),
            $this->connection->getPostProcessor()
        ))->toBe($this->builder)
        ->getQuery()->not->toBeInstanceOf(QueryBuilder::class);
});

it('has scope where clause', function () {
    $user = User::factory()->create();

    expect($this->builder)
        ->scope($user)->toBe($this->builder)
        ->getQuery()->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($where) => $where
                ->toBeArray()
                ->toEqualCanonicalizing([
                    'type' => 'Basic',
                    'column' => $this->builder->qualifyColumn('scope'),
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
        ->getQuery()->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($where) => $where
                ->toBeArray()
                ->toEqualCanonicalizing([
                    'type' => 'Basic',
                    'column' => $this->builder->qualifyColumn('widget'),
                    'operator' => '=',
                    'value' => Widgets::serializeWidget($widget),
                    'boolean' => 'and',
                ])
            ));
});
