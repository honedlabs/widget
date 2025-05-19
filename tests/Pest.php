<?php

declare(strict_types=1);

use Honed\Widget\Tests\Stubs\Product;
use Honed\Widget\Tests\Stubs\Status;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function product(?string $name = null): Product
{
    return Product::create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->word(),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}
