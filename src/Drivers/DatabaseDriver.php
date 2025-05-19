<?php

namespace Honed\Widget\Drivers;

use Honed\Widget\Contracts\Driver;
use Honed\Widget\Facades\Widgets;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Carbon;

class DatabaseDriver implements Driver
{
    /**
     * The database connection.
     *
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * The user configuration.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct(
        DatabaseManager $db,
        Dispatcher $events,
        Repository $config,
        string $name
    ) {
        $this->db = $db;
        $this->events = $events;
        $this->config = $config;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function get($scope, $group = null)
    {
        return $this->newQuery()
            ->where('scope', Widgets::serializeScope($scope))
            ->where('group', $group)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($widget, $scope, $group = null)
    {
        return $this->newQuery()
            ->where('widget', $widget)
            ->where('scope', Widgets::serializeScope($scope))
            ->where('group', $group)
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function set($widget, $scope, $group = null, $order = 0)
    {
        $this->newQuery()
            ->upsert([
                'group' => $group,
                'name' => $widget,
                'scope' => Widgets::serializeScope($scope),
                'order' => $order,
                self::CREATED_AT => $now = Carbon::now(),
                self::UPDATED_AT => $now,
            ], [
                'group',
                'name',
                'scope',
            ], [
                'order',
                self::UPDATED_AT,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update($widget, $scope, $group = null, $order = 0)
    {
        return (bool) $this->newQuery()
            ->where('name', $widget)
            ->where('scope', Widgets::serializeScope($scope))
            ->where('group', $group)
            ->update([
                'order' => $order,
                self::UPDATED_AT => Carbon::now(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($widget, $scope, $group = null)
    {
        return (bool) $this->newQuery()
            ->where('name', $widget)
            ->where('scope', Widgets::serializeScope($scope))
            ->where('group', $group)
            ->delete();
    }

    /**
     * Create a new table query.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newQuery()
    {
        return $this->connection()->table(
            $this->config->get("widget.drivers.{$this->name}.table") ?? 'widgets'
        );
    }

    /**
     * The database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function connection()
    {
        return $this->db->connection(
            $this->config->get("widget.drivers.{$this->name}.connection") ?? null
        );
    }
}
