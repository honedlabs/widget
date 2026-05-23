<?php

declare(strict_types=1);

namespace Honed\Widget\Models;

use Honed\Widget\Casts\PositionCast;
use Honed\Widget\Casts\ScopeCast;
use Honed\Widget\Casts\WidgetCast;
use Honed\Widget\Concerns\InteractsWithDatabase;
use Honed\Widget\QueryBuilder;
use Honed\Widget\WidgetBuilder;
use Honed\Widget\WidgetCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $widget
 * @property string $scope
 * @property string|null $position
 * @property array<string, mixed>|null $data
 * @property \Carbon\CarbonImmutable $created_at
 * @property \Carbon\CarbonImmutable $updated_at
 */
class Widget extends Model
{
    use InteractsWithDatabase;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    public $guarded = [];

    /**
     * Begin querying the model.
     *
     * @return WidgetBuilder<self>
     */
    public static function query()
    {
        /** @var WidgetBuilder<self> */
        return parent::query();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'widget' => WidgetCast::class,
            'scope' => ScopeCast::class,
            'position' => PositionCast::class,
            'data' => $this->dataCast(),
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * Set the cast for the data attribute.
     */
    public function dataCast(): string
    {
        return 'array';
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        return $this->table ??= $this->getTableName();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  QueryBuilder  $query
     * @return WidgetBuilder<self>
     */
    public function newEloquentBuilder($query)
    {
        return new WidgetBuilder($query);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        return new QueryBuilder($this->getConnection());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<array-key, \Illuminate\Database\Eloquent\Model>  $models
     * @return WidgetCollection
     */
    public function newCollection(array $models = []): WidgetCollection
    {
        return new WidgetCollection($models);
    }
}
