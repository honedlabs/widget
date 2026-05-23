<?php

declare(strict_types=1);

namespace App\Widgets;

use App\Models\User;
use Honed\Widget\Widget;

class UserCountWidget extends Widget
{
    protected $name = 'count';

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return User::query()->getquery()->count();
    }
}
