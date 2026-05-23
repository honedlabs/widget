<?php

declare(strict_types=1);

namespace Honed\Widget;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * @template TModel of \Honed\Widget\Models\Widget
 *
 * @extends \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @mixin \Honed\Widget\QueryBuilder
 */
class WidgetBuilder extends EloquentBuilder
{
    /**
     * The base query builder instance.
     *
     * @var QueryBuilder
     */
    protected $query;

    /**
     * Create a new Eloquent query builder instance.
     */
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    /**
     * Get the underlying query builder instance.
     *
     * @return QueryBuilder
     */
    public function getQuery()
    {
        /** @var QueryBuilder */
        return parent::getQuery();
    }

    /**
     * Set the underlying query builder instance.
     *
     * @param  QueryBuilder  $query
     * @return $this
     */
    public function setQuery($query)
    {
        return parent::setQuery($query);
    }

    /**
     * Add a `where` clause to the query for the scope.
     *
     * @return $this
     */
    public function scope(mixed $scope, string $column = 'scope'): static
    {
        $this->query->scope($scope, $this->qualifyColumn($column));

        return $this;
    }

    /**
     * Add a `where` clause to the query for the widget.
     *
     * @return $this
     */
    public function widget(mixed $widget, string $column = 'widget'): static
    {
        $this->query->widget($widget, $this->qualifyColumn($column));

        return $this;
    }
}
