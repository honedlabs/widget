<?php

declare(strict_types=1);

namespace Honed\Widget\Casts;

use Honed\Widget\Facades\Widgets;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<string, mixed>
 */
class GridAreaCast extends PositionCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! is_string($value)) {
            return null;
        }

        return Widgets::convertToGridArea($value);
    }
}
