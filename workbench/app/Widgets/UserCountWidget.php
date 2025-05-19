<?php

declare(strict_types=1);

namespace Workbench\App\Widgets;

use Honed\Widget\Widget;
use Workbench\App\Models\User;

class UserCountWidget extends Widget
{
    protected $name = 'user-count';
    
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return User::query()->getquery()->count();
    }
}