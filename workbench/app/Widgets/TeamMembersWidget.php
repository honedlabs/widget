<?php

declare(strict_types=1);

namespace App\Widgets;

use Honed\Widget\Widget;

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
