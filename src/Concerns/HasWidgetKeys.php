<?php

namespace Honed\Widget\Concerns;

use Honed\Widget\Facades\Widgets;

trait HasWidgetKeys
{
    /**
     * Create the key identifier for the widget.
     */
    public function key($scope, $group = null)
    {
        $scope = Widgets::serializeScope($scope);

        return $group ? "{$group}.{$scope}" : $scope;
    }

    // Add to the array of widgets
}
