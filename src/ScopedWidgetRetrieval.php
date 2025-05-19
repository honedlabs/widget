<?php

namespace Honed\Widget;

use Illuminate\Support\Arr;

class ScopedWidgetRetrieval
{
    /**
     * The widget driver
     * 
     * @var \Honed\Widget\Drivers\Decorator
     */
    protected $driver;

    /**
     * The widget model scope.
     * 
     * @var array<int, mixed>
     */
    protected $scope = [];

    /**
     * Create a new scoped widget retrieval instance.
     * 
     * @param \Honed\Widget\Drivers\Decorator $driver
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Add a scope to the retrieval.
     * 
     * @param mixed|array<int,mixed> ...$scope
     * @return $this
     */
    public function for(...$scope)
    {
        $scope = Arr::flatten($scope);

        $this->scope = array_merge($this->scope, $scope);

        return $this;
    }

    /**
     * The scope to pass to the driver.
     * 
     * @return array<mixed>
     */
    public function scope()
    {
        return $this->scope ?: [null];
    }
}

