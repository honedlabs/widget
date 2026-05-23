<?php

declare(strict_types=1);

namespace Honed\Widget\Drivers;

use Honed\Widget\Concerns\Resolvable;
use Honed\Widget\Contracts\CanListWidgets;
use Honed\Widget\Contracts\Driver;
use Honed\Widget\Events\WidgetCreated;
use Honed\Widget\Events\WidgetDeleted;
use Honed\Widget\Events\WidgetUpdated;
use Honed\Widget\PendingWidgetInteraction;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

class Decorator implements Driver
{
    use Macroable {
        __call as macroCall;
    }
    use Resolvable;

    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The driver instance.
     *
     * @var Driver
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
     * @var Container
     */
    protected $container;

    /**
     * Create a new driver decorator instance.
     *
     * @param  (callable():mixed)  $defaultScopeResolver
     */
    public function __construct(
        string $name,
        Driver $driver,
        callable $defaultScopeResolver,
        Container $container
    ) {
        $this->name = $name;
        $this->driver = $driver;
        $this->defaultScopeResolver = $defaultScopeResolver;
        $this->container = $container;
    }

    /**
     * Dynamically call the underlying driver instance.
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

        return tap(new PendingWidgetInteraction($this), function ($retrieval) use ($name) {
            if ($name !== 'for' && ($scope = ($this->defaultScopeResolver)()) !== null) {
                $retrieval->for($scope);
            }
        })->{$name}(...$parameters);
    }

    /**
     * Create a new pending widget interaction instance.
     */
    public function for(mixed $scope = null): PendingWidgetInteraction
    {
        return (new PendingWidgetInteraction($this))
            ->for($scope ?? $this->defaultScope());
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $scope): array
    {
        return $this->driver->get($scope);
    }

    /**
     * {@inheritdoc}
     */
    public function set(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): void
    {
        $this->driver->set($widget, $scope, $data, $position);

        Event::dispatch(new WidgetCreated($widget, $scope, $data, $position));
    }

    /**
     * {@inheritdoc}
     */
    public function update(mixed $widget, mixed $scope, mixed $data = null, mixed $position = null): bool
    {
        $outcome = $this->driver->update($widget, $scope, $data, $position);

        Event::dispatch(new WidgetUpdated($widget, $scope, $data, $position));

        return $outcome;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(mixed $widget, mixed $scope): bool
    {
        $outcome = $this->driver->delete($widget, $scope);

        Event::dispatch(new WidgetDeleted($widget, $scope));

        return $outcome;
    }

    /**
     * Retrieve the widget's name.
     *
     * @param  string  $widget
     * @return string
     */
    public function name($widget)
    {
        return $this->resolveWidget($widget);
    }

    /**
     * Get the underlying driver instance.
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * Set the container instance used by the decorator.
     *
     * @return $this
     */
    public function setContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Retrieve the default scope.
     */
    protected function defaultScope(): mixed
    {
        return ($this->defaultScopeResolver)();
    }

    /**
     * Check if the driver supports listing widgets.
     *
     * @throws RuntimeException
     */
    protected function checkIfCanListWidgets(): CanListWidgets
    {
        if (! $this->driver instanceof CanListWidgets) {
            throw new RuntimeException(
                "The [{$this->name}] driver does not support listing widgets."
            );
        }

        /** @var CanListWidgets */
        return $this->driver;
    }
}
