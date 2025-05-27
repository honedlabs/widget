<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Workbench\App\Models\User;

/**
 * @template TModel of \Workbench\App\Models\User
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class UserFactory extends \Workbench\Database\Factories\UserFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = User::class;
}
