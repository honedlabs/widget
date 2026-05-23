<?php

declare(strict_types=1);

namespace Honed\Widget;

use Honed\Widget\Concerns\Resolvable;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder
{
    use Resolvable;

    /**
     * Create a new query builder instance.
     */
    public function __construct(Connection $connection)
    {
        parent::__construct(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );
    }

    /**
     * Add a `where` clause to the query for the scope.
     */
    public function scope(mixed $scope, string $column = 'scope'): self
    {
        return $this->where($column, $this->resolveScope($scope));
    }

    /**
     * Add a `where` clause to the query for the widget.
     */
    public function widget(mixed $widget, string $column = 'widget'): self
    {
        return $this->where($column, $this->resolveWidget($widget));
    }
}
