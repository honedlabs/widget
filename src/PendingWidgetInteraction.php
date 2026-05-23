<?php

declare(strict_types=1);

namespace Honed\Widget;

use Honed\Widget\Drivers\Decorator;

class PendingWidgetInteraction
{
    /**
     * The widget driver
     *
     * @var Decorator
     */
    protected $driver;

    /**
     * The widget model scope.
     *
     * @var mixed
     */
    protected $scope;

    /**
     * Create a new scoped widget retrieval instance.
     */
    public function __construct(Decorator $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the scope to pass to the driver.
     *
     * @return $this
     */
    public function for(mixed $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get the scope to pass to the driver.
     */
    public function getScope(): mixed
    {
        return $this->scope;
    }

    /**
     * Get the widgets for the scope.
     *
     * @return array<int, mixed>
     */
    public function get(): array
    {
        return $this->driver->get($this->getScope());
    }

    /**
     * Set the widget.
     */
    public function set(mixed $widget, mixed $data = null, mixed $position = null): void
    {
        $this->driver->set($widget, $this->getScope(), $data, $position);
    }

    /**
     * Update the widget.
     */
    public function update(mixed $widget, mixed $data = null, mixed $position = null): bool
    {
        return $this->driver->update($widget, $this->getScope(), $data, $position);
    }

    /**
     * Delete the widget.
     */
    public function delete(mixed $widget): bool
    {
        return $this->driver->delete($widget, $this->getScope());
    }
}
