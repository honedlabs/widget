<?php

declare(strict_types=1);

namespace Workbench\App\Widgets;

use Honed\Widget\Widget;
use Workbench\App\Models\User;

class TeamMembersWidget extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return 5;
    }
}