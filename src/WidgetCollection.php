<?php

declare(strict_types=1);

namespace Honed\Widget;

use Illuminate\Database\Eloquent\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Collection<array-key, \Honed\Widget\Models\Widget>
 */
class WidgetCollection extends Collection
{
    /**
     * Resolve the underlying widgets.
     *
     * @return $this
     */
    // public function resolve(): static
    // {
    //     return $this;
    // }
}
