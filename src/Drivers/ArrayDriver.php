<?php

namespace Honed\Widget\Drivers;

use Honed\Widget\Contracts\Driver;
use Illuminate\Contracts\Events\Dispatcher;

class ArrayDriver implements Driver
{
    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * {@inheritdoc}
     */
    public function get($scope, $group = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function exists($widget, $scope, $group = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function set($widget, $scope, $group = null, $order = 0)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function update($widget, $scope, $group = null, $order = 0)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function delete($widget, $scope, $group = null)
    {

    }    
}
