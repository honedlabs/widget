<?php

declare(strict_types=1);

namespace Honed\Widget\Casts;

use Honed\Widget\Facades\Widgets;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Database\Eloquent\Model;

class ScopeCast implements CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Widgets::serializeScope($value);
    }
}
