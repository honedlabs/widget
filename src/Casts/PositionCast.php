<?php

declare(strict_types=1);

namespace Honed\Widget\Casts;

use Honed\Widget\Facades\Widgets;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class PositionCast implements CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return match (true) {
            is_null($value),
            is_string($value) => $value,
            // @phpstan-ignore-next-line argument.type
            is_array($value) => Widgets::convertToPosition(...$value),
            default => throw new InvalidArgumentException(
                'Unable to cast value to position.'
            ),
        };
    }
}
