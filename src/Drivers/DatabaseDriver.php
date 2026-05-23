<?php

declare(strict_types=1);

namespace Honed\Widget\Drivers;

use Honed\Widget\Concerns\InteractsWithDatabase;
use Honed\Widget\Concerns\Resolvable;
use Honed\Widget\Contracts\Driver;
use Honed\Widget\QueryBuilder;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Carbon;

class DatabaseDriver implements Driver
{
    use InteractsWithDatabase;
    use Resolvable;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    public const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    public const UPDATED_AT = 'updated_at';

    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The database connection.
     *
     * @var DatabaseManager
     */
    protected $db;

    /**
     * Create a new database driver instance.
     */
    public function __construct(string $name, DatabaseManager $db)
    {
        $this->name = $name;
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $scope): array
    {
        return $this->newQuery()
            ->scope($scope)
            ->get()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function set(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): void
    {
        $this->newQuery()
            ->insert($this->fill(compact('widget', 'scope', 'data', 'position')));
    }

    /**
     * {@inheritdoc}
     */
    public function update(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): bool
    {
        return (bool) $this->newQuery()
            ->scope($scope)
            ->widget($widget)
            ->update([
                'data' => $data,
                'position' => $position,
                self::UPDATED_AT => Carbon::now(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(mixed $widget, mixed $scope): bool
    {
        return (bool) $this->newQuery()
            ->scope($scope)
            ->widget($widget)
            ->delete();
    }

    /**
     * Create an array of values to be inserted.
     *
     * @param  array{widget: mixed, scope: mixed, data: mixed, position: mixed}  $values
     * @return array<string, mixed>
     */
    protected function fill(array $values): array
    {
        return [
            'widget' => $this->resolveWidget($values['widget']),
            'scope' => $this->resolveScope($values['scope']),
            'data' => json_encode($values['data'], JSON_THROW_ON_ERROR),
            'position' => $values['position'],
            self::CREATED_AT => $now = Carbon::now(),
            self::UPDATED_AT => $now,
        ];
    }

    /**
     * Create a new table query.
     */
    protected function newQuery(): QueryBuilder
    {
        return (new QueryBuilder($this->connection()))
            ->from($this->getTableName());
    }

    /**
     * The database connection.
     */
    protected function connection(): Connection
    {
        return $this->db->connection($this->getConnectionName());
    }
}
