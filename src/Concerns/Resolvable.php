<?php

declare(strict_types=1);

namespace Honed\Widget\Concerns;

use Honed\Widget\Facades\Widgets;

trait Resolvable
{
    /**
     * Resolve the scope.
     */
    public function resolveScope(mixed $scope): string
    {
        return Widgets::serializeScope($scope);
    }

    /**
     * Resolve the widget name.
     */
    public function resolveWidget(mixed $widget): string
    {
        return Widgets::serializeWidget($widget);
    }
}
