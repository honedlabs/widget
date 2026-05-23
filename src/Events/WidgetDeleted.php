<?php

declare(strict_types=1);

namespace Honed\Widget\Events;

use Illuminate\Foundation\Events\Dispatchable;

class WidgetDeleted
{
    use Dispatchable;

    /**
     * Create a new widget deleted event.
     */
    public function __construct(
        public mixed $widget,
        public mixed $scope,
    ) {}
}
