<?php

namespace Honed\Widget\Drivers;

use Honed\Widget\Concerns\HasWidgetKeys;
use Honed\Widget\Contracts\Driver;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;

class CacheDriver implements Driver
{
    use HasWidgetKeys;

    /**
     * The cache manager.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The user configuration.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct(
        CacheManager $cache,
        Dispatcher $events,
        Repository $config
    ) {
        $this->cache = $cache;
        $this->events = $events;
        $this->config = $config;
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

    public function getDuration()
    {
        return $this->config->get('widget.cache.duration');
    }

    public function getPrefix()
    {
        return $this->config->get('widget.cache.prefix');
    }

    /**
     * Cache key is: group.scope
     *
     * Value is [
     *  widget => order,
     * ]
     */
}
