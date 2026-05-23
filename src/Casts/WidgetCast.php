<?php

declare(strict_types=1);

namespace Honed\Widget\Casts;

use Honed\Widget\Facades\Widgets;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Honed\Widget\Widget, mixed>
 */
class WidgetCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return Widgets::make($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return Widgets::serializeWidget($value);
    }
}
