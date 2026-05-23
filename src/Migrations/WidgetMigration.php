<?php

declare(strict_types=1);

namespace Honed\Widget\Migrations;

use Honed\Widget\Concerns\InteractsWithDatabase;
use Illuminate\Database\Migrations\Migration;

abstract class WidgetMigration extends Migration
{
    use InteractsWithDatabase;

    /**
     * Get the migration connection name.
     */
    public function getConnection(): string
    {
        return $this->getConnectionName();
    }
}
