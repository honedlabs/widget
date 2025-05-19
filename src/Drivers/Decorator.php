<?php

namespace Honed\Widget\Drivers;

use Honed\Widget\Contracts\Driver;
use Honed\Widget\Contracts\WidgetScopeable;
use Honed\Widget\Events\WidgetDeleted;
use Honed\Widget\Events\WidgetUpdated;
use Honed\Widget\ScopedWidgetRetrieval;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Traits\Macroable;

class Decorator implements Driver
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The driver name.
     *
     * @var string
     */
    protected $name;

    /**
     * The driver being decorated.
     *
     * @var \Honed\Widget\Contracts\Driver
     */
    protected $driver;

    /**
     * The default scope resolver.
     *
     * @var callable(): mixed
     */
    protected $defaultScopeResolver;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new driver decorator instance.
     * 
     * @param string $name
     * @param \Honed\Widget\Contracts\Driver $driver
     * @param (callable():mixed)|null $defaultScopeResolver
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(
        $name,
        $driver,
        $defaultScopeResolver,
        $container
    ) {
        $this->name = $name;
        $this->driver = $driver;
        $this->defaultScopeResolver = $defaultScopeResolver;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get($scope, $group = null)
    {
        $this->driver->get($scope, $group);

        // Event::dispatch(new WidgetRetrieved($widget, $scope, $item));

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($widget, $scope, $group = null, $order = 0)
    {
        $widget = $this->resolveWidget($widget);

        $scope = $this->resolveScope($scope);

        $this->driver->set($widget, $scope, $group, $order);

        Event::dispatch(new WidgetUpdated($widget, $scope));
    }

    /**
     * {@inheritdoc}
     */
    public function update($widget, $scope, $group = null, $order = 0)
    {
        $widget = $this->resolveWidget($widget);

        $scope = $this->resolveScope($scope);

        $outcome = $this->driver->update($widget, $scope, $group, $order);

        Event::dispatch(new WidgetUpdated($widget, $scope, $group, $order));
        
        return $outcome;

    }

    /**
     * {@inheritdoc}
     */
    public function delete($widget, $scope, $group = null)
    {
        $widget = $this->resolveWidget($widget);

        $scope = $this->resolveScope($scope);

        $this->driver->delete($widget, $scope, $group);

        Event::dispatch(new WidgetDeleted($widget, $scope));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($widget, $scope, $group = null)
    {
        $widget = $this->resolveWidget($widget);

        $scope = $this->resolveScope($scope);
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function purge(...$widgets)
    // {
    //     $this->driver->purge(...$widgets);
    // 

    /**
     * Retrieve the widget's class.
     * 
     * @param string $name
     * @return \Honed\Widget\Contracts\Widget
     */
    public function instance($name)
    {
        $this->container->make($name);
    }

    /**
     * Retrieve the widget's name.
     * 
     * @param string $widget
     * @return string
     */
    public function name($widget)
    {
        return $this->resolveWidget($widget);
    }

    /**
     * Resolve the widget by name.
     * 
     * @param string $widget
     * @return string
     */
    protected function resolveWidget($widget)
    {
        // return $this->
    }

    /**
     * Resolve the scope.
     *
     * @param  mixed  $scope
     * @return mixed
     */
    protected function resolveScope($scope)
    {
        return $scope instanceof WidgetScopeable
            ? $scope->toWidgetIdentifier($this->name)
            : $scope;
    }

    /**
     * Retrieve the default scope.
     *
     * @return mixed
     */
    protected function defaultScope()
    {
        return ($this->defaultScopeResolver)();
    }

    /**
     * Get the underlying driver instance.
     * 
     * @return \Honed\Widget\Contracts\Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set the container instance used by the decorator.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Dynamically create a pending feature interaction.
     *
     * @param  string  $name
     * @param  array<mixed>  $parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        if (static::hasMacro($name)) {
            return $this->macroCall($name, $parameters);
        }

        return tap(new ScopedWidgetRetrieval($this), function ($retrieval) use ($name) {
            if ($name !== 'for' && ($scope = ($this->defaultScopeResolver)()) !== null) {
                $retrieval->for($scope);
            }
        })->{$name}(...$parameters);
    }
}