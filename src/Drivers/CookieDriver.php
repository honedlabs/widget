<?php

namespace Honed\Widget\Drivers;

use Honed\Widget\Contracts\Driver;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Cookie\CookieJar;
use Illuminate\Support\Facades\Cookie;

class CookieDriver implements Driver
{
    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cookie jar.
     *
     * @var \Illuminate\Cookie\CookieJar
     */
    protected $cookies;

    public function __construct(
        CookieJar $cookies,
        Dispatcher $events,
        Repository $config
    ) {
        $this->events = $events;
        $this->cookies = $cookies;
        // $this->config = $config;
    }

    /**
     * Cookie name is: group.scope
     *
     * Value is [
     *  widget => order,
     * ]
     */

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
    public function exists($widget, $scope, $group = null) {}

    /**
     * {@inheritdoc}
     */
    public function set($widget, $scope, $group = null, $order = 0) {}

    /**
     * {@inheritdoc}
     */
    public function update($widget, $scope, $group = null, $order = 0) {}

    /**
     * {@inheritdoc}
     */
    public function delete($widget, $scope, $group = null) {}
}
