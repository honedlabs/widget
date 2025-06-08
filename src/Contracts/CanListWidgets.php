<?php

namespace Honed\Widget\Contracts;

interface CanListWidgets
{
    /**
     * Clear all widgets under a given scope and group.
     * 
     * @param string $scope
     * @param string|null $group
     * @return void
     */
    public function clear($scope, $group = null);
}